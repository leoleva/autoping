<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Address;
use App\Entity\Job;
use App\Entity\User;
use App\Enum\JobStatus;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;

class JobCreator
{
    public function __construct(
      private EntityManagerInterface $entityManager,
    ) {

    }

    public function createNewJob(
        User $user,
        string $title,
        string $text,
        DateTime $duetTo,
        Money $money,
        JobStatus $jobStatus,
        Address $address
    ): void {
        $this->entityManager->persist($address);
        $this->entityManager->flush($address);

        $job = new Job();
        $job->setUserId($user->getId());
        $job->setUser($this->entityManager->getReference(User::class, $user->getId()));
        $job->setTitle($title);
        $job->setText($text);
        $job->setDueTo($duetTo);
        $job->setAmount($money->getAmount());
        $job->setCurrency($money->getCurrency());
        $job->setStatus($jobStatus);
        $job->setAddressId($address->getId());
        $job->setAddress($address);
        $job->setCreatedAt(new DateTime());
        $job->setUpdatedAt(new DateTime());

        $this->entityManager->persist($job);
        $this->entityManager->flush($job);
    }
}
