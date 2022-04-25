<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Entity\Address;
use App\Entity\User;
use App\Enum\UserType;
use App\Services\UserCreator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class UserCreatorTest extends TestCase
{
    private MockObject $userPasswordHasher;
    private MockObject $entityManager;

    private UserCreator $userCreator;

    protected function setUp(): void
    {
        $this->userCreator = new UserCreator(
            $this->userPasswordHasher = $this->createMock(UserPasswordHasher::class),
            $this->entityManager = $this->createMock(EntityManagerInterface::class),
        );
    }

    public function testCreateUser(): void
    {
        $password = 'password';
        $address = (new Address())->setId(1);

        $this->userPasswordHasher
            ->expects(self::once())
            ->method('hashPassword')
            ->with(self::anything(), $password);

        $this->entityManager
            ->expects(self::once())
            ->method('persist');

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        self::assertInstanceOf(
            User::class,
            $this->userCreator->createUser('buyer', 'email', $password, $address, null, null)
        );
    }
}
