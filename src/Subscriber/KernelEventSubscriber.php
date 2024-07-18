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
use Symfony\Component\VarDumper\VarDumper;

class KernelEventSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() : array
    {
        return  [
            'kernel.controller' => "onControllerLoad",
        ];
    }

    public function __construct(){}


    public function onControllerLoad(ControllerEvent $event){
      $request = $event->getRequest();
      if($request->get("_route") != "app_user_convo"){
          if (isset($_COOKIE['conversation'])) {
              unset($_COOKIE['conversation']);
              setcookie('conversation', '', -1, '/');
          }
      }
    }


}