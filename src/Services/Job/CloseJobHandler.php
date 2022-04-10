<?php

declare(strict_types=1);

namespace App\Services\Job;

use App\Entity\Job;
use App\Enum\JobStatus;
use Doctrine\ORM\EntityManagerInterface;

class CloseJobHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function close(Job $job): void
    {
        $job->setStatus(JobStatus::Closed);

        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }
}
