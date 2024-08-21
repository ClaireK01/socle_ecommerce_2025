<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\EcommerceOrder;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\EcommerceOrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

//#[Route(requirements: ['_locale' => 'en|fr'])]
class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('{_locale}/profile', name: 'app_user')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm('App\Form\UserType', $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->render('user/show.html.twig', [
            'form'=>$form->createView(),
        ]);
    }


    #[Route('{_locale}/profile/messages', name: 'app_user_message')]
    public function showMessagesAction(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
           return $this->redirectToRoute('app_homepage');
        }

        return $this->render('app/user/messages.html.twig', []);
    }


    #[Route('{_locale}/profile/orders', name: 'app_user_orders')]
    public function listOrdersAction(Request $request, EcommerceOrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        if(!$user){
           return $this->redirectToRoute('app_homepage');
        }

        $orders = $orderRepository->getUserOrder($user);

        return $this->render('app/user/orders.html.twig', [
            "orders" => $orders,
        ]);
    }

    #[Route('{_locale}/profile/orders/{id}/show', name: 'app_user_order_show')]
    public function showOrdersAction(EcommerceOrder $order, Request $request, EcommerceOrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('app/user/order_show.html.twig', [
            "order" => $order,
        ]);
    }

    #[Route('/messages', name: 'app_user_convo', methods: ['POST'])]
    public function conversationAction(Request $request, ConversationRepository $conversationRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $from = $userRepository->find($request->request->get('from'));
        $to = $userRepository->find($request->request->get('to'));

        if(!$from || !$to){
            return $this->redirectToRoute('app_homepage');
        }

        $conversations = $conversationRepository->getConversationByMember($from, $to);

        if(!$conversations){
            $conversation = new Conversation();
            $conversation->addMember($from);
            $conversation->addMember($to);
            $entityManager->persist($conversation);
            $entityManager->flush();
        }else{
            $conversation = $conversations[0];
        }

        setcookie('conversation', $conversation->getId(), 0, '/');

        return $this->render('user/conversation.html.twig', [
            "conversation" => $conversation
        ]);
    }

    #[Route('{_locale}/profile/{id}/invoice', name: 'app_user_invoice')]
    public function generateInvoice(EcommerceOrder $order, Pdf $generator){

//        if($this->getUser()){
//            return $this->redirectToRoute('app_homepage');
//        }

        $user = $this->getUser();

//        if($order->getShippingAddress()->getUser() != $user && $order->getFacturationAddress()->getUser() != $user){
//            return $this->redirectToRoute('app_homepage');
//        }

       return new PdfResponse( $generator->getOutputFromHtml(
                $this->renderView('app/user/invoice.html.twig', [
                    'order' => $order
                ]),
            ),'invoice.pdf'

       );

    }


}
