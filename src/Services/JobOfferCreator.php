<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Job;
use App\Entity\JobOffer;
use App\Entity\User;
use App\Enum\JobOfferStatus;
use App\Enum\JobStatus;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use App\Services\Job\JobUpdater;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;

class JobOfferCreator
{
    public function __construct(
        private JobRepository $jobRepository,
        private EntityManagerInterface $entityManager,
        private Mailer $mailer,
        private UserRepository $userRepository,
        private JobUpdater $jobUpdater
    ) {
    }

    public function createJobOffer(
        int $jobId,
        User $user,
        string $text,
        DateTime $createdAt,
        Money $price,
        JobOfferStatus $status,
    ): void {
        $jobOffer = new JobOffer();

        $jobOffer->setUserId($user->getId());
        $jobOffer->setText($text);
        $jobOffer->setCreatedAt($createdAt);
        $jobOffer->setAmount($price->getAmount());
        $jobOffer->setCurrency($price->getCurrency());
        $jobOffer->setStatus($status);
        $jobOffer->setJobId($jobId);

        $jobOffer->setJob($this->entityManager->getReference(Job::class, $jobId));
        $jobOffer->setUser($this->entityManager->getReference(User::class, $user->getId()));

        $job = $this->jobRepository->getById($jobId);
        $jobOwner = $this->userRepository->getUserById($job->getUserId());

        $this->mailer->offerCreated($jobOwner, $job);

        if ($job->getStatus() === JobStatus::New) {
            $this->jobUpdater->updateJobStatusToPending($job);
        }

        $this->entityManager->persist($jobOffer);
        $this->entityManager->flush();
    }
}
