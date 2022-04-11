<?php

declare(strict_types=1);

namespace App\Services\UserReview;

use App\Entity\User;
use App\Entity\UserReview;
use App\Enum\UserType;
use App\Repository\UserReviewRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class UserReviewHandler
{
    public function __construct(
        private UserReviewRepository $userReviewRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createReview(
        int $rating,
        string $text,
        User $reviewer,
        User $user,
        DateTime $createdAt,
        int $jobId
    ): void {
        $userReviews = new UserReview();
        $userReviews->setRating($rating);
        $userReviews->setReview($text);
        $userReviews->setReviewer($reviewer);
        $userReviews->setReviewerId($reviewer->getId());
        $userReviews->setUser($user);
        $userReviews->setCreatedAt($createdAt);
        $userReviews->setJobId($jobId);

        $this->entityManager->persist($userReviews);
        $this->entityManager->flush();
    }

    public function updateReview(int $id, string $text, int $rating): void
    {
        $userReview = $this->userReviewRepository->getById($id);

        $userReview->setRating($rating);
        $userReview->setReview($text);

        $this->entityManager->persist($userReview);
        $this->entityManager->flush();
    }

    public function remove(int $id): void
    {
        $userReview = $this->userReviewRepository->getById($id);

        $this->entityManager->persist($userReview);
        $this->entityManager->flush();
    }
}
