<?php

declare(strict_types=1);

namespace App\Services\JobOffer;

use App\Entity\Job;
use App\Entity\JobOffer;
use App\Entity\User;
use App\Enum\JobOfferStatus;
use App\Repository\JobRepository;

class AcceptedOfferValidator
{
    public function __construct(
      private JobRepository $jobRepository
    ) {
    }

    public function isUserJobAssigneeByJobAndUser(Job $job, User $user): bool
    {
        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()
            ->filter(fn(JobOffer $jobOffer): bool => $jobOffer->getStatus() === JobOfferStatus::Accepted)
            ->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return false;
        }

        if ($acceptedOffer->getUserId() !== $user->getId()) {
            return false;
        }

        return true;
    }

    public function isUserJobAssigneeByIdAndUser(int $jobId, User $user): bool
    {
        $job = $this->jobRepository->getById($jobId);

        return $this->isUserJobAssigneeByJobAndUser($job, $user);
    }
}
