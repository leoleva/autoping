<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    public function __construct(
        private MailerInterface $mailer
    ) {

    }

    public function offerCreated(\App\Entity\User $user, \App\Entity\Job $job): void
    {
        $email = (new Email())
            ->from('admin@autoping.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('You got an offer!')
            ->html('<p>You got offer for "'.$job->getTitle().'"!</p>');

        $this->mailer->send($email);
    }
}
