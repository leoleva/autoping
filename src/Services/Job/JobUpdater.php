<?php

declare(strict_types=1);

namespace App\Services\Job;

use App\Entity\Address;
use App\Enum\JobStatus;
use App\Repository\AddressRepository;
use App\Repository\JobRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;

class JobUpdater
{
    public function __construct(
        private JobRepository $jobRepository,
        private AddressRepository $addressRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function updateJob(
        int $jobId,
        string $title,
        string $text,
        DateTime $dueTo,
        Money $money,
        Address $address
    ): void {
        $job = $this->jobRepository->getById($jobId);
        $jobAddress = $this->addressRepository->getAddressById($job->getAddressId());

        $jobAddress->setCountryId($address->getCountryId());
        $jobAddress->setStateId($address->getStateId());
        $jobAddress->setCityId($address->getCityId());

        $job->setTitle($title);
        $job->setText($text);
        $job->setDueTo($dueTo);
        $job->setAmount($money->getAmount());
        $job->setCurrency($money->getCurrency());

        $this->entityManager->persist($jobAddress);
        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }

    public function updateJobStatus(int $jobId, JobStatus $status): void
    {
        $job = $this->jobRepository->getById($jobId);

        $job->setStatus($status);

        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }
}
