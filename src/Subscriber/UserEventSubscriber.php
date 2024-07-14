<?php

namespace App\Subscriber;

use App\Entity\User;
use App\Events\Events;
use App\Events\UserWebSocketEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() : array
    {
        return  [
            Events::USER_CONNECETD => "onUserConnected",
            Events::USER_DISCONNECTED => "onUserDisconnected",
        ];
    }

    public function __construct(private readonly EntityManagerInterface $em){}


    public function onUserConnected(UserWebSocketEvent $event){
        $user = $event->getUser();
        $user->setStatus(User::CONNECTED);
        $user->addSessionId($event->getResourceId());
        $this->em->persist($user);
        $this->em->flush();
    }

    public function onUserDisconnected(UserWebSocketEvent $event){
        $user = $event->getUser();
        $user->setStatus(User::DISCONNECTED);
        $this->em->persist($user);
        $this->em->flush();
    }

}