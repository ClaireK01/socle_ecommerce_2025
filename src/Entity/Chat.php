<?php

namespace App\Entity;

use App\Events\ConversationWebSocketEvent;
use App\Events\Events;
use App\Events\UserWebSocketEvent;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Twig\Environment;

class Chat implements MessageComponentInterface
{


    protected $clients;
    private $serializer;
    private $logger;
    private $repository;
    private $dispatcher;
    private $twig;
    private $conversationRepository;
    private EntityManagerInterface $em;
    private ?User $user;

    public function __construct(EncoderInterface $serializer, LoggerInterface $logger,
                                UserRepository $repository, Environment $twig, EntityManagerInterface $em,
                                EventDispatcherInterface $dispatcher, ConversationRepository $conversationRepository
    ){
        $this->clients =  new \SplObjectStorage;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->twig = $twig;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->conversationRepository = $conversationRepository;
    }


    //TODO : trouver comment faire en sorte que cookie "conversation" puisse etre recuperer apres avoir ete mis sur la page
    // -> faire en sorte que la page aille chercher les cookies dans le bon path (si possible eviter de les mettre sur l'acceuil)

    public function onOpen(ConnectionInterface $conn)
    {

        $cookies = $this->getCookies($conn->httpRequest);
        $user = $this->getUser($cookies);
        $conversation = $this->getConversation($cookies);

        $this->clients->attach($conn);
        $count = count($this->clients);
        $this->sendToClient($conn, $this->clients, $this->encode(['count' => $count, 'clients' => $this->getTemplate($conn)]));
        if($conversation){
            $this->sendToClient($conn, $this->clients, $this->encode(['conversation' => $this->getConversationTemplate($conn)]));
        }
        $this->dispatcher->dispatch(new UserWebSocketEvent($user, $conn->resourceId), Events::USER_CONNECETD);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $cookies = $this->getCookies($from->httpRequest);
        $conversation = $this->getConversation($cookies);
        $user = $this->getUser($cookies);
        $array = json_decode($msg);

        if($conversation){
            $this->dispatcher->dispatch(new ConversationWebSocketEvent($conversation, $user, $array->mess), Events::CONVERSATION_MESSAGE_SENT);

            if($conversation->getId() == $array->convo){
                $this->sendTemplateToSpecificClient($from, $this->clients, $conversation->getMembers()->toArray());
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $cookies = $this->getCookies($conn->httpRequest);
        $user = $this->getUser($cookies);

        $this->dispatcher->dispatch(new UserWebSocketEvent($user), Events::USER_DISCONNECTED);
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        var_dump($e->getMessage());
        $conn->close();
    }

    public function sendToClient($from, $clients, $message){
        foreach($clients as $c){
            if($from == $c){
                $c->send($message);
            }
        }
    }

    public function sendToOtherClients($from, $clients, $message){
        foreach($clients as $c){
            if($from !== $c){
                $c->send($message);
            }
        }
    }

//    public function sendToSpecificClient($from, $clients, $members, $message){
//
//        $cookies = $this->getCookies($from->httpRequest);
//        $user = $this->getUser($cookies);
//        $connected = false;
//        $member = null;
//
//        foreach($clients as $c){
//            if($from != $c){
//                foreach ($members as $m){
//                    if($m->findResourceId($c->resourceId)){
//                        $member = $m;
//                        break;
//                    }
//                }
//
//                if($member && $member != $user && $this->repository->getLastResourceIdUser($c->resourceId) == $member){
//                    $c->send($message);
//                    $connected = true;
//                }
//            }else{
//                $c->send($message);
//            }
//        }
//
//        if(!$connected){
//            var_dump('notify');
//        }
//    }

    //trouver moyen d'eviter code dupliquer
    public function sendTemplateToSpecificClient($from, $clients, $members){

        $cookies = $this->getCookies($from->httpRequest);
        $user = $this->getUser($cookies);
        $connected = false;
        $member = null;

        foreach($clients as $c){
            if($from != $c){
                foreach ($members as $m){
                    if($m->findResourceId($c->resourceId)){
                        $member = $m;
                        break;
                    }
                }

                if($member && $member != $user && $this->repository->getLastResourceIdUser($c->resourceId) == $member){
                    $message = $this->encode(['conversation' => $this->getConversationTemplate($c)]);
                    $c->send($message);
                    $connected = true;
                }
            }else{

                $message = $this->encode(['conversation' => $this->getConversationTemplate($c)]);
                $c->send($message);
            }
        }

        if(!$connected){
            var_dump('notify');
        }
    }

    public function encode($data){
        return $this->serializer->encode($data, 'json');
    }

    public function getTemplate($conn){
        $cookies = $this->getCookies($conn->httpRequest);
        $user = $this->getUser($cookies);
        $users =$this->repository->findAll();


        return $this->twig->render('websocket/clients.html.twig', [
            "clients" => $this->clients,
            "user" => $user,
            "users" => $users
        ]);
    }

    public function getConversationTemplate($conn) : string{
        $cookies = $this->getCookies($conn->httpRequest);
        $user = $this->getUser($cookies);
        $conversation = $this->getConversation($cookies);

        return $this->twig->render('websocket/messages.html.twig', [
            "conversation" => $conversation,
            "user" => $user,
        ]);
    }


    public function getCookies($request) : array{
       $cookies = [];
       $array =  explode(';', $request->getHeader('Cookie')[0]);
       foreach ($array as $cookie){
           $val = explode('=', $cookie);
           $cookies[str_replace(' ', '', $val[0])] = $val[1];
       }

       return $cookies;
    }

    public function getUser($cookies){
        if(array_key_exists('user', $cookies)){
            return $this->repository->find($cookies['user']);
        }

        return null;
    }

    public function getConversation($cookies){
        if(array_key_exists('conversation', $cookies)){
            return $this->conversationRepository->find($cookies['conversation']);
        }

        return null;
    }
}