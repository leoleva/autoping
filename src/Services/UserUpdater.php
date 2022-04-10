<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Address;
use App\Enum\UserType;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserUpdater
{
    public function __construct(
        private AddressRepository $addressRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {}

    public function updateUser(
        int $id,
        string $email,
        string $password,
        ?string $bankAccount,
        ?string $name,
        Address $address
    ): void {
        $user = $this->userRepository->getUserById($id);
        $userAddress = $this->addressRepository->getAddressById($user->getAddressId());

        $userAddress->setCountryId(
            $address->getCountryId()
        );

        if ($address->getStateId() !== 0) {
            $userAddress->setStateId($address->getStateId());
        } else {
            $userAddress->setStateId(null);
        }

        if ($address->getCityId() !== 0) {
            $userAddress->setCityId($address->getCityId());
        } else {
            $userAddress->setCityId(null);
        }

        if ($user->getUserType() === UserType::Specialist) {
            $user->setBankAccount($bankAccount);
            $user->setName($name);
        }

        $user->setEmail($email);

        if ($password !== '') {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->persist($userAddress);
        $this->entityManager->flush();
    }
}