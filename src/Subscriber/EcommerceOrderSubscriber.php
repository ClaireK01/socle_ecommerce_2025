<?php

namespace App\Subscriber;

use App\Entity\EcommerceOrder;
use App\Entity\ResourceId;
use App\Entity\User;
use App\Events\EcommerceOrderEvent;
use App\Events\Events;
use App\Events\MailEvent;
use App\Events\UserWebSocketEvent;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\VarDumper\VarDumper;

class EcommerceOrderSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() : array
    {
        return  [
            Events::ORDER_STATE_CHANGED => "onOrderStateChanged",
        ];
    }

    public function __construct(private readonly EventDispatcherInterface $dispatcher, private readonly EntityManagerInterface $em){}


    public function onOrderStateChanged(EcommerceOrderEvent $event)
    {
        $order = $event->getOrder();
        $state = $event->getState();
        $send = false;
        

        if($order->getStatus() != EcommerceOrder::STATES['canceled']){
            if($state == EcommerceOrder::STATES['confirmed']){
                if($order->getStatus() == EcommerceOrder::STATES['payed']) {
                    $order->setStatus($state);
                    $send = true;
                }
            }elseif ($state == EcommerceOrder::STATES['shipped']){
                if($order->getStatus() == EcommerceOrder::STATES['confirmed']){
                    $order->setStatus($state);
                    $send = true;
                }
            }elseif ($state == EcommerceOrder::STATES['done']){
                if($order->getStatus() == EcommerceOrder::STATES['shipped']){
                    $order->setStatus($state);
                    $send = true;
                }
            }elseif ($state == EcommerceOrder::STATES['canceled']){
                $order->setStatus($state);
                $send = true;
            }
        }

        $this->em->persist($order);
        $this->em->flush($order);

        if($send){
            $event = new MailEvent($event->getLocale(), $order);
            $event->addSendTo(MailEvent::TYPES['client'], $order->getShippingAddress()->getUser()->getEmail());
            $this->dispatcher->dispatch($event, Events::MAIL_MAJ_CLIENT_ORDER);
        }
    }


}