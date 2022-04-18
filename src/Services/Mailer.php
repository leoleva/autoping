<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Job;
use App\Entity\JobOffer;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Mailer
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private string $appHost,
    ) {
    }

    public function offerCreated(User $user, Job $job): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@autoping.lt', 'AutoPing.lt'))
            ->to($user->getEmail())
            ->subject('Gautas naujas pasiūlymas')
            ->htmlTemplate('email/mail-template.html.twig')
            ->context([
                'email_title' => 'Gautas naujas pasiūlymas',
                'email_text' => 'Jūsų darbui ' . $job->getTitle() . ' buvo pateiktas naujas pasiūlymas',
                'button' => true,
                'button_text' => 'Peržiūrėti pasiūlymus',
                'button_url' => $this->appHost . $this->urlGenerator->generate('author_view_job', ['id' => $job->getId()]),
            ]);

        $this->mailer->send($email);
    }

    public function offerAccepted(User $user, Job $job, JobOffer $jobOffer): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@autoping.lt', 'AutoPing.lt'))
            ->to($user->getEmail())
            ->subject('Su jūsų užsakymu buvo sutikta')
            ->htmlTemplate('email/mail-template.html.twig')
            ->context([
                'email_title' => 'Su jūsų užsakymu buvo sutikta',
                'email_text' => 'Su jūsų užsakymu ' . $jobOffer->getText()  . ' skelbime ' . $job->getTitle() . ' buvo sutikta',
                'button' => true,
                'button_text' => 'Peržiūrėti užsakymus',
                'button_url' => $this->appHost . $this->urlGenerator->generate('specialist_job_list'),
            ]);

        $this->mailer->send($email);
    }

    public function offerDeclined(User $user, Job $job): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@autoping.lt', 'AutoPing.lt'))
            ->to($user->getEmail())
            ->subject('Jūsų pasiūlymas buvo atmestas')
            ->htmlTemplate('email/mail-template.html.twig')
            ->context([
                'email_title' => 'Jūsų pasiūlymas buvo atmestas',
                'email_text' => 'Jūsų pasiūlymas skelbime ' . $job->getTitle() . ' buvo atmestas',
                'button' => true,
                'button_text' => 'Peržiūrėti pasiūlymus',
                'button_url' => $this->appHost . $this->urlGenerator->generate('author_view_job', ['id' => $job->getId()]),
            ]);

        $this->mailer->send($email);
    }

    public function jobIsReadyForReview(User $user, Job $job): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('admin@autoping.lt', 'AutoPing.lt'))
            ->to($user->getEmail())
            ->subject('Duomenys paruošti peržiūrai')
            ->htmlTemplate('email/mail-template.html.twig')
            ->context([
                'email_title' => 'Duomenys paruošti peržiūrai',
                'email_text' => 'Skelbimo' . $job->getTitle() . ' duomenys yra paruošti peržiūrai',
                'button' => true,
                'button_text' => 'Peržiūrėti duomenis',
                'button_url' => $this->appHost . $this->urlGenerator->generate('review_job_photos', ['id' => $job->getId()]),
            ]);

        $this->mailer->send($email);
    }
}
