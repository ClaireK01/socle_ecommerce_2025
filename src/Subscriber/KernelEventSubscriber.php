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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\VarDumper\VarDumper;

class KernelEventSubscriber implements EventSubscriberInterface {

    private $sam;

    /**
     * @InjectParams({
     *     "sam" = @Inject("security.token_storage")
     * })
     */
    public function __construct(TokenStorageInterface $sam)
    {

        $this->sam = $sam;
    }

    public static function getSubscribedEvents() : array
    {
        return  [
            'kernel.controller' => "onControllerLoad",
        ];
    }

    public function onControllerLoad(ControllerEvent $event){

        $request = $event->getRequest();
      if($request->get("_route") != "app_user_convo"){
          if (isset($_COOKIE['conversation'])) {
              unset($_COOKIE['conversation']);
              setcookie('conversation', '', -1, '/');
          }
      }
        $user = $this->sam->getToken() ?-> getUser();

        if($user){
            $this->setUser($user);
        }else{
            $this->removeUser();
        }
        
    }

    public function setUser($user){
        if(!isset($_COOKIE['user'])){
            setcookie('user', $user->getId(), 0 , '/');
        }elseif( $_COOKIE['user'] != $user->getId()){
            $this->removeUser();
            setcookie('user', $user->getId(), 0 , '/');
        }
    }

    public function removeUser(){

        if(isset($_COOKIE['user'])) {
            unset($_COOKIE['user']);
            setcookie('user', '', -1, '/');
        }
    }


}