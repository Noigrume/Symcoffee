<?php

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }
    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        // récupérer l'utilisateur en lignes
        $currentUser = $this->security->getUser();
        //récupérer la commmande
        $purchase = $purchaseSuccessEvent->getPurchase();
        //ecrire mailer
        $mail = new TemplatedEmail();
        $mail->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->from("test@mail.com")
            ->subject("La commande ({$purchase->getId()}) est confirmée")
            ->htmlTemplate('mails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);
        //envoyer mail
        $this->mailer->send($mail);
        $this->logger->info("email envoyé");
    }
}
