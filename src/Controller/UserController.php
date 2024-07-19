<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

#[Route('/profile')]
class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/', name: 'app_user')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            $this->redirectToRoute('app_homepage');
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

}
