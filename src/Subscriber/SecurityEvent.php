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
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\VarDumper\VarDumper;

class SecurityEvent implements EventSubscriberInterface {

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

        if (!isset($_COOKIE['user']) && $user instanceof User) {
            setcookie('user', $user->getId(), 0 , '/');
        }

    }


}