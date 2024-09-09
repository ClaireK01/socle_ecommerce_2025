<?php

namespace App\Subscriber;

use App\Entity\Cart;
use App\Entity\ResourceId;
use App\Entity\User;
use App\Entity\UserSession;
use App\Events\Events;
use App\Events\UserWebSocketEvent;
use App\Repository\CartRepository;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\VarDumper\VarDumper;

class SecurityEventSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() : array
    {
        return  [
            'security.interactive_login' => "onInteractiveLogin",
        ];
    }

    public function __construct(
        private readonly CartRepository $cartRepository, private readonly OrderService $orderService,
        private readonly EntityManagerInterface $entityManager
    ){}


    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        //gestion du cart a la connexion
        if ($user instanceof User) {
            $activeCart = $this->cartRepository->getActiveCartUser($user);
            $activeCart = count($activeCart) > 0 ? $activeCart[0] : null;
            $session = $event->getRequest()->getSession();
            $cartId = $session->get('cart');
            if(!$cartId){
                if($activeCart){
                    $session->set('cart', $activeCart->getId());
                    $session->set('shopCount', $this->orderService->getCartInfos($activeCart)['total_quantity'] ?? 0);
                }
            }else{
                $cart = $this->cartRepository->find($cartId);
                if($cart){
                    if(count($cart->getCardProducts()) <= 0 &&  $activeCart){
                        $session->set('cart', $activeCart->getId());
                        $session->set('shopCount', $this->orderService->getCartInfos($activeCart)['total_quantity'] ?? 0);
                        $this->orderService->removeCart($cart);
                    }else{
                        if(!$cart->getUserSession()){
                            $userSession = new UserSession();
                            $userSession->setUser($user);
                            $userSession->setSessionId($session->getId());
                            $cart->setUserSession($userSession);
                            $this->entityManager->persist($cart);
                        }

                        if($activeCart && $activeCart !== $cart){
                            $this->orderService->removeCart($activeCart);
                        }
                    }
                }
            }

            $this->entityManager->flush();
        }
    }
}