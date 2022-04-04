<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class User
{
    public function __construct(
        private AddressRepository $addressRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {}

    public function updateUser(int $id, string $email, string $password, Address $address): array
    {
        $errors = [];

        $user = $this->userRepository->getUserById($id);
        $userAddress = $this->addressRepository->getAddressById($user->getAddressId());

        if ($user->getEmail() !== $email && count($this->userRepository->findBy(['email' => $email])) > 0) {
            $errors[] = 'Vartotojas jau egzistuoja';
        }

        if (strlen($password) !== 0 && ($password) < 3) {
            $errors[] = 'Slaptažodis per trumpas';
        }

        if (count($errors) === 0) {
            $userAddress->setCountryId(
                $address->getCountryId()
            );

            $userAddress->setStateId(
                $address->getStateId()
            );

            $userAddress->setCityId(
                $address->getCityId()
            );

            $user->setEmail($email);

            if (strlen($password) !== 0) {
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

            return ['login_successful' => true];
        }

        return ['login_errors' => $errors];
    }
}