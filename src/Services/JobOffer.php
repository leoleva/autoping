<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\JobOffer as JobOfferEntity;
use App\Entity\User as UserEntity;
use App\Enum\JobOfferStatus;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;

class JobOffer
{
    public function __construct(
        private JobRepository $jobRepository,
        private EntityManagerInterface $entityManager,
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function createJobOffer(
        int $jobId,
        UserEntity $user,
        ?string $text,
        DateTime $createdAt,
        Money $price,
        JobOfferStatus $status,
    ): void {
        $jobOffer = new JobOfferEntity();
        $jobOffer->setUserId($user->getId());
        $jobOffer->setText($text);
        $jobOffer->setCreatedAt($createdAt);
        $jobOffer->setAmount($price->getAmount());
        $jobOffer->setCurrency($price->getCurrency());
        $jobOffer->setStatus($status);
        $jobOffer->setJobId($jobId);

        $this->entityManager->persist($jobOffer);
        $this->entityManager->flush($jobOffer);

        $job = $this->jobRepository->getById($jobId);
        $jobOwner = $this->userRepository->getUserById($job->getUserId());

        $this->mailer->offerCreated($jobOwner, $job);
    }
}
