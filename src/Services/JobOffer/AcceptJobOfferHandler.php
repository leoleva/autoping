<?php

declare(strict_types=1);

namespace App\Services\JobOffer;

use App\Entity\JobOffer;
use App\Enum\JobOfferStatus;
use App\Enum\JobStatus;
use App\Repository\JobOfferRepository;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;

class AcceptJobOfferHandler
{
    public function __construct(
        private JobOfferRepository $jobOfferRepository,
        private EntityManagerInterface $entityManager,
        private Mailer $mailer
    ) {
    }

    public function acceptJobOffer(JobOffer $jobOffer): void
    {
        $job = $jobOffer->getJob();
        $otherOffers = $this->jobOfferRepository->getOffersByJobId($jobOffer->getJobId());

        foreach ($otherOffers as $otherOffer) {
            if ($otherOffer->getStatus() !== JobOfferStatus::New) {
                continue;
            }

            if ($otherOffer->getId() === $jobOffer->getId()) {
                continue;
            }

            $otherOffer->setStatus(JobOfferStatus::Declined);
            $this->mailer->offerDeclined($otherOffer->getUser(), $otherOffer->getJob());
            $this->entityManager->persist($otherOffer);
        }

        $this->mailer->offerAccepted($jobOffer->getUser(), $jobOffer->getJob(), $jobOffer);

        $jobOffer->setStatus(JobOfferStatus::Accepted);
        $job->setStatus(JobStatus::Active);

        $job->setAmount($jobOffer->getAmount());
        $job->setCurrency($jobOffer->getCurrency());

        $this->entityManager->persist($jobOffer);
        $this->entityManager->persist($job);

        $this->entityManager->flush();
    }
}
