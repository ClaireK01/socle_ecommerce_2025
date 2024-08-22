<?php

namespace App\Subscriber;

use App\Events\Events;
use App\Events\MailEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MailSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() : array
    {
        return  [
            Events::MAIL_ORDER => "onMailOrder",
        ];
    }

    public function __construct(private readonly EntityManagerInterface $em, private readonly MailerInterface $mailer, private readonly TranslatorInterface $translator,
                                private readonly  Environment $twig){}


    public function onMailOrder(MailEvent $event){

        $order = $event->getEntity();

        foreach ($event->getEmails() as $type => $email){

            $message = $this->translator->trans("order.message." . $event->getLabelType($type), [], "mailer", $event->getLocale());
            $title = $this->translator->trans(
                "order.title." . $event->getLabelType($type),
                $type == MailEvent::TYPES['client'] ? ['number' => $order->getNumber()] : [],
                "mailer", $event->getLocale()
            );

            $template = $this->twig->render('app/mail/order_notification.html.twig', [
                'title' => $title,
                'message' => $message,
                'order' => $order,
            ]);
            $this->send($template, $email);
        }
    }

    public function send($template, $address){
        $email = (new Email())
            ->from($_ENV['MAILER_SENDER'])
            ->to($address)
            ->priority(Email::PRIORITY_NORMAL)
            ->subject('ticket')
            ->html($template);
//            ->attachFromPath($this->writer->getPath() . $pdf, 'Ticket.pdf', 'application/pdf'); //ajout de pdf

        $this->mailer->send($email);
    }

}