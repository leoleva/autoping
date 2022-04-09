<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserReviewRepository::class)]
class UserReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $user_id;

    #[ORM\Column(type: 'integer')]
    private $reviewer_id;

    #[ORM\Column(type: 'text')]
    private $review;

    #[ORM\Column(type: 'integer')]
    private $rating;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'integer')]
    private int $jobId;

    #[ORM\ManyToOne(targetEntity: User::class )]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: User::class )]
    #[ORM\JoinColumn(name: 'reviewer_id', referencedColumnName: 'id', nullable: false)]
    private User $reviewer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getReviewerId(): int
    {
        return $this->reviewer_id;
    }

    public function setReviewerId(int $reviewer_id): self
    {
        $this->reviewer_id = $reviewer_id;

        return $this;
    }

    public function getReview(): string
    {
        return $this->review;
    }

    public function setReview(string $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): UserReview
    {
        $this->user = $user;

        return $this;
    }

    public function getReviewer(): User
    {
        return $this->reviewer;
    }

    public function setReviewer(User $reviewer): UserReview
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }

    public function setJobId(int $jobId): UserReview
    {
        $this->jobId = $jobId;

        return $this;
    }
}
