<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Address;
use App\Entity\Job as JobEntity;
use App\Enum\JobStatus;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;

class Job
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {

    }

    public function createNewJob(
        \App\Entity\User $user,
        string $title,
        string $text,
        \DateTime $duetTo,
        Money $money,
        JobStatus $jobStatus,
        Address $address
    ): void {
        $this->entityManager->persist($address);
        $this->entityManager->flush($address);

        $job = new JobEntity();
        $job->setUserId($user->getId());
        $job->setTitle($title);
        $job->setText($text);
        $job->setDueTo($duetTo);
        $job->setAmount($money);
        $job->setCurrency($money);
        $job->setStatus($jobStatus);
        $job->setAddressId($address->getId());
        $job->setCreatedAt(new \DateTime());
        $job->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($job);
        $this->entityManager->flush($job);
    }
}
