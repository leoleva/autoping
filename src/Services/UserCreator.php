<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Address;
use App\Entity\User;
use App\Enum\UserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreator
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createUser(
        string $type,
        string $email,
        string $password,
        Address $address,
        ?string $account,
        ?string $name
    ): User {
        $user = new User();
        $user->setUserType(UserType::from($type));
        $user->setEmail($email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setAddressId($address->getId());
        $user->setCreatedAt(new DateTime());

        if ($user->getUserType() === UserType::Specialist) {
            $user->setBankAccount($account);
            $user->setName($name);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
