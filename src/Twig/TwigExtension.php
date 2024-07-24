<?php

namespace App\Twig;

use App\Entity\Cart;
use App\Entity\Conversation;
use App\Repository\CartRepository;
use App\Repository\ConversationRepository;
use App\Service\OrderService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{

    public function __construct(    private readonly ConversationRepository $conversationRepository, private readonly OrderService $orderService,
                                    private readonly CartRepository $cartRepository
    )
    {

    }

    public function getFilters()
    {
        return [];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLastMessage', [$this, 'getLastMessage']),
            new TwigFunction('getShopCount', [$this, 'getShopCount']),
        ];
    }

    public function getLastMessage($user, $member)
    {
        /** @var Conversation $conversation */
        $conversations = $this->conversationRepository->getConversationByMember($user, $member);
        $conversation = null;
        if(!empty($conversations)){
            $conversation = $conversations[0];
        }

        if($conversation){
            return $conversation->getMessages()->last();
        }

        return null;
    }

    public function getShopCount($session){
        $cart = $this->cartRepository->findOneBy(['session_id' => $session->getId()]);

        if($cart instanceof Cart){
            return $this->orderService->getCartInfos($cart)['total_quantity'];
        }

        return 0;
    }
}