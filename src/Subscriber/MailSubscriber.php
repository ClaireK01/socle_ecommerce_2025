<?php

namespace App\Subscriber;

use App\Entity\ResourceId;
use App\Entity\User;
use App\Events\Events;
use App\Events\MailEvent;
use App\Events\UserWebSocketEvent;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() : array
    {
        return  [
            Events::MAIL_ORDER => "onMailOrder",
        ];
    }

    public function __construct(private readonly EntityManagerInterface $em, private readonly MailerInterface $mailer, private readonly TranslatorInterface $translator){}


    public function onMailOrder(MailEvent $event){
        $user = $event->getUser();
        switch ($event->getType()){

            case MailEvent::TYPES['client']:
                $message = '';
            case MailEvent::TYPES['pro']:
                $message = '';
            case MailEvent::TYPES['admin']:
                $message = '';
            default:
                $message = '';

        }

        $email = (new TemplatedEmail())
            ->from($_ENV['MAILER_SENDER'])
            ->to($event->getUser()->getEmail())
            ->priority(Email::PRIORITY_NORMAL)
            ->subject('ticket')
            ->htmlTemplate('app/mail/order_notification.html.twig', [

            ]);
//            ->attachFromPath($this->writer->getPath() . $pdf, //ajout de pdf
//                $transaction->getDate()->format('Y-m-d_H:i:s') . '_Ticket.pdf', 'application/pdf');

    }

}