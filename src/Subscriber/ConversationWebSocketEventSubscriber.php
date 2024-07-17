<?php

namespace App\Subscriber;

use App\Entity\Message;
use App\Events\ConversationWebSocketEvent;
use App\Events\Events;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\VarDumper\VarDumper;

class ConversationWebSocketEventSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() : array
    {
        return  [
            Events::CONVERSATION_MESSAGE_SENT => "onConversationMessageSent",
        ];
    }

    public function __construct(private readonly EntityManagerInterface $em, private readonly ConversationRepository $conversationRepository,
                                private readonly MessageRepository $messageRepository
    ){}


    public function onConversationMessageSent(ConversationWebSocketEvent $event) : void {
        $conversation = $event->getConversation();

        $message = new Message();
        $message->setContent($event->getMessage());
        $message->setSender($event->getUser());
        $conversation->addMessage($message);

        $this->em->persist($conversation);
        $this->em->flush();
    }


}