<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Conversation;
use App\Entity\EcommerceOrder;
use App\Entity\Picture;
use App\Entity\User;
use App\Events\EcommerceOrderEvent;
use App\Events\Events;
use App\Form\AddressType;
use App\Form\EcommerceOrderType;
use App\Form\UserType;
use App\Repository\AddressRepository;
use App\Repository\ConversationRepository;
use App\Repository\EcommerceOrderRepository;
use App\Repository\UserRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf;
use Nette\Utils\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\VarDumper\VarDumper;

//#[Route(requirements: ['_locale' => 'en|fr'])]
class UserController extends AbstractController
{
    private $em;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository){
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    #[Route('{_locale}/profile', name: 'app_user')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm(UserType::class, $user, ['type' => UserType::TYPE['profile']]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $old = key_exists("oldpassword", $request->request->all()) ? $request->request->all()['oldpassword'] : null;
            $new = $form->get('password')->getData();
            if( !$old && !$new ){
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('error', "Vos information ont bien été enregistrées");
            }else{
                if($hasher->isPasswordValid($user, $old)){
                    $hash = $hasher->hashPassword($user, $new);
                    $user->setPassword($hash);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $entityManager->refresh($user);
                    $this->addFlash('error', "Mot de passe changé");
                }else{
                    $this->addFlash('error', "L'ancien mot de passe est invalide");
                    $entityManager->refresh($user);
                    return $this->redirectToRoute('app_user');
                }
            }
        }

        return $this->render('user/show.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    #[Route('{_locale}/profile/address/{id}/delete', name: 'app_user_address_delete', methods:[ "GET", "POST"])]
    public function editProfile(Address $address, Request $request, EntityManagerInterface $entityManager): Response
    {
        /***@var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        if($request->getMethod() == "POST") {
            $user->removeAddress($address);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user');
        }

        if($request->isXmlHttpRequest()){
            $view = $this->renderView('app/user/adress_form.html.twig', ['address' => $address]);
            return new JsonResponse(['view' => $view], 200);
        }

        return $this->redirectToRoute('app_user');

    }

//    #[Route('{_locale}/profile/{id}/edit', name: 'app_user_edit', methods: "POST")]
//    public function editProfile(User $user, Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $content = json_decode($request->getContent(), true);
//        $field = array_key_first($content);
//        $form = $this->createForm('App\Form\UserType', $user, ['type' => UserType::TYPE['profile'], 'field' => $field]);
//        $form->submit($content);
//
//        if($form->isValid() && $this->isCsrfTokenValid($user->getId(), $content['token'])){
//            $entityManager->persist($user);
//            $entityManager->flush();
//            //get new field value as it is registered in the db
//            $reflexion = new \ReflectionClass(User::class);
//            $method = $reflexion->getMethod('get'.ucfirst($field));
//
//            return new JsonResponse(['data' => $method->invoke($user)], 200);
//        }
//
//        return new JsonResponse(['Invalid data'], 201);
//    }

    #[Route('{_locale}/user/address/add', name: 'app_user_address_add', methods: ['GET', 'POST'])]
    #[Route('{_locale}/user/address/{id}/edit', name: 'app_user_address_edit', methods: ['GET', 'POST'])]
    public function cartAddressAddAction(Request $request, EntityManagerInterface $entityManager,RouterInterface $router,
                                         AddressRepository $addressRepository, $id = null)
    {
        $user = $this->getUser();
        $address= null;
        if($id){ $address = $addressRepository->find($id); }
        if(!$address){
            $address = new Address();
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->addAddress($address);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user');
        }

        return $this->render('app/cart/form_addresses.html.twig', [
            'type' => 'profile',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('{_locale}/profile/{id}/edit-picture', name: 'app_user_edit_picture', methods: "POST")]
    public function editProfilePicture(User $user, Request $request, EntityManagerInterface $entityManager, FileService $fileService, KernelInterface $kernel): Response
    {
        $upload = new UploadedFile($_FILES['pfp']['tmp_name'], $_FILES['pfp']['name'], $_FILES['pfp']['type']);
        $res = $fileService->uploadFile($upload);

        if($res['path'] !== ''){
            $originalPic = $user->getFirstPicture();
            //en cas de requete double
            if($originalPic->getPath() != $res['path']){
                if($originalPic->getPath() !== Picture::SANS_IMG){
                    $user->removePicture($originalPic);
                    $path = $kernel->getProjectDir() . "/public/" . $originalPic->getPath();
                    if(file_exists($path)){
                        unlink($path);
                    }
                    $entityManager->remove($originalPic);
                }

                $pfp = new Picture();
                $pfp->setPath($res['path']);
                $user->addPicture($pfp);
                $entityManager->persist($pfp);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return new JsonResponse(['message' => $res['message'], 'path' => '../'.$res['path'] ], $res['status']);

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

        return $this->render('app/user/orders.html.twig', []);
    }

    #[Route('{_locale}/profile/orders/{id}/show', name: 'app_user_order_show')]
    public function showOrderAction(EcommerceOrder $order, Request $request, EcommerceOrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('app/user/order_show.html.twig', [
            "order" => $order,
        ]);
    }

    #[Route('{_locale}/profile/orders/{id}/cancel', name: 'app_user_order_cancel', methods: ['GET', 'POST'])]
    public function cancelOrderAction(EcommerceOrder $order, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm(EcommerceOrderType::class, $order);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $order->setStatus(EcommerceOrder::CANCELED);
            $em->persist($order);
            $em->flush();
            return $this->redirectToRoute('app_user');
        }



        if($request->isXmlHttpRequest()){
            $view = $this->renderView('app/user/order_form.html.twig', ['form' => $form->createView()]);
            return new JsonResponse(['view' => $view], 200);
        }

        return $this->redirectToRoute('app_user');
    }

    #[Route('{_locale}/profile/request', name: 'app_user_request')]
    public function listRequestAction(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('app/user/requests.html.twig', []);
    }

    #[Route('{_locale}/profile/request/{id}/show', name: 'app_user_request_show')]
    public function showRequestAction( EcommerceOrder $order, Request $request, EcommerceOrderRepository $orderRepository, EventDispatcherInterface $dispatcher): Response
    {
        $user = $this->getUser();
        if(!$user || $order->getRequestReceiver() !== $user){
            return $this->redirectToRoute('app_homepage');
        }

        if($request->getMethod() == 'POST' && $request->request->get("state")){
            $state = $request->request->get('state') ?? null;
            if($state){
                $dispatcher->dispatch(new EcommerceOrderEvent($order, $state, $request->getLocale()), Events::ORDER_STATE_CHANGED);
            }
            return $this->redirectToRoute('app_user_request');
        }

        return $this->render('app/user/request_show.html.twig', [
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
        if (!isset($_COOKIE['conversation']) || $_COOKIE['conversation'] != $conversation->getId()) {
            setcookie('conversation', $conversation->getId(), 0, '/');
        }


        return $this->render('user/conversation.html.twig', [
            "conversation" => $conversation
        ]);
    }

    #[Route('{_locale}/profile/{id}/invoice', name: 'app_user_invoice')]
    public function generateInvoice(EcommerceOrder $order, Pdf $generator){
        //Renvoyer une erreur plutot que redirection

        $user = $this->getUser();

        if(!$user){
            return $this->redirectToRoute('app_homepage');
        }

        if($order->getUser() != $user && $order->getUser() != $user){
            return $this->redirectToRoute('app_homepage');
        }

       return new PdfResponse( $generator->getOutputFromHtml(
                $this->renderView('app/user/invoice.html.twig', [
                    'order' => $order
                ]),
            ),'invoice.pdf'
       );

    }


}
