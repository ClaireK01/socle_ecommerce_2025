<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
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

    #[Route('/messages/{from}/{to}', name: 'app_user_convo')]
    public function conversationAction(Request $request, User $from, User $to, ConversationRepository $conversationRepository, EntityManagerInterface $entityManager): Response
    {
        $conversation = $conversationRepository->getConversationByMember($from, $to)[0];

        if(!$conversation){
            $conversation = new Conversation();
            $conversation->addMember($from);
            $conversation->addMember($to);
            $entityManager->persist($conversation);
            $entityManager->flush();
        }

        setcookie('conversation', $conversation->getId());

        return $this->render('user/conversation.html.twig', [
            "conversation" => $conversation
        ]);
    }

}
