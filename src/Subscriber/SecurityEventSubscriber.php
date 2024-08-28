<?php

namespace App\Subscriber;

use App\Entity\ResourceId;
use App\Entity\User;
use App\Events\Events;
use App\Events\UserWebSocketEvent;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
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

    public function __construct(){}


    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $this->setUser($user);
        }
    }

//    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event){
//        $user = $event->getAuthenticationToken()->getUser();
//        $this->setUser($user);
//    }

    public function setUser($user){
        if(!isset($_COOKIE['user'])){
            setcookie('user', $user->getId(), 0 , '/');
        }elseif ( $_COOKIE['user'] != $user->getId()){
           $this->removeUser();
            setcookie('user', $user->getId(), 0 , '/');
        }
    }

    public function removeUser(){
        unset($_COOKIE['user']);
        setcookie('user', '', -1, '/');
    }


}