<?php

namespace App\Subscriber;

use App\Entity\EcommerceOrder;
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
            Events::MAIL_ORDER => "onOrder",
            Events::MAIL_MAJ_CLIENT_ORDER => "onMajClientOrder",
        ];
    }

    public function __construct(private readonly EntityManagerInterface $em, private readonly MailerInterface $mailer, private readonly TranslatorInterface $translator,
                                private readonly  Environment $twig){}


    public function onOrder(MailEvent $event){

        $order = $event->getEntity();

        foreach ($event->getSendTo() as $type => $email){

            $message = $this->translator->trans("order.message." . $event->getLabelType($type), [], "mailer", $event->getLocale());
            $title = $this->translator->trans("order.title." . $event->getLabelType($type), $type == MailEvent::TYPES['client'] ? ['number' => $order->getNumber()] : [], "mailer", $event->getLocale());

            $template = $this->twig->render('app/mail/order_notification.html.twig', [
                'title' => $title,
                'message' => $message,
                'order' => $order,
            ]);
            $this->send($template, $email);
        }
    }

    public function onMajClientOrder(MailEvent $event){
        /** @var EcommerceOrder $order */
        $order = $event->getEntity();
        $email = $event->getSendTo()[MailEvent::TYPES['client']];

        $title = $this->translator->trans("majorder.status.title.client",  [], "mailer", $event->getLocale());
        $message = $this->translator->trans("majorder.status.message.client", ['statut' => $order->getStatusLabel()], "mailer", $event->getLocale());

        $template = $this->twig->render('app/mail/order_notification.html.twig', [
            'title' => $title,
            'message' => $message,
            'order' => $order,
        ]);

        $this->send($template, $email);
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