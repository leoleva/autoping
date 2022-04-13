<?php

declare(strict_types=1);

namespace App\Services\JobOffer;

use App\Entity\JobOffer;
use App\Enum\JobOfferStatus;
use Doctrine\ORM\EntityManagerInterface;

class JobOfferStatusHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function closeJobOffer(JobOffer $jobOffer): void
    {
        $jobOffer->setStatus(JobOfferStatus::Closed);

        $this->entityManager->persist($jobOffer);
        $this->entityManager->flush();
    }

    public function acceptJobOffer(JobOffer $jobOffer): void
    {
        $jobOffer->setStatus(JobOfferStatus::Accepted);

        $this->entityManager->persist($jobOffer);
        $this->entityManager->flush();
    }

    public function declineJobOffer(JobOffer $jobOffer): void
    {
        $jobOffer->setStatus(JobOfferStatus::Declined);

        $this->entityManager->persist($jobOffer);
        $this->entityManager->flush();
    }
}
