<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Address;
use App\Entity\Job as JobEntity;
use App\Enum\JobStatus;
use App\Repository\AddressRepository;
use App\Repository\JobRepository;
use App\Validator\JobUpdateValidator;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;

class Job
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobRepository $jobRepository,
        private JobUpdateValidator $jobUpdateValidator,
        private AddressRepository $addressRepository
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
        $job->setAmount($money->getAmount());
        $job->setCurrency($money->getCurrency());
        $job->setStatus($jobStatus);
        $job->setAddressId($address->getId());
        $job->setCreatedAt(new \DateTime());
        $job->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($job);
        $this->entityManager->flush($job);
    }

    public function delete(int $id): void
    {
        $job = $this->jobRepository->getById($id);

        $this->entityManager->remove($job);
        $this->entityManager->flush();
    }

    public function updateJob(
        int $jobId,
        \App\Entity\User $user,
        string $title,
        string $text,
        \DateTime $dueTo,
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
}
