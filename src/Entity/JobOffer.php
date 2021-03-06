<?php

namespace App\Entity;

use App\Enum\JobOfferStatus;
use App\Enum\JobStatus;
use App\Repository\JobOfferRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $user_id;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'datetime')]
    private DateTime $created_at;

    #[ORM\Column(type: 'string', length: 255)]
    private string $amount;

    #[ORM\Column(type: 'string', length: 255)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 255, enumType: JobOfferStatus::class)]
    private JobOfferStatus $status;

    #[ORM\Column(type: 'integer', unique: false)]
    private int $job_id;

    #[ORM\ManyToOne(targetEntity: Job::class, cascade: ['persist'], inversedBy: 'jobOffer')]
    private Job $job;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getStatus(): JobOfferStatus
    {
        return $this->status;
    }

    public function setStatus(JobOfferStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getJobId(): int
    {
        return $this->job_id;
    }

    public function setJobId(int $job_id): self
    {
        $this->job_id = $job_id;

        return $this;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): JobOffer
    {
        $this->job = $job;

        return $this;
    }

    public function getLithuanianStatusNamingForOffer(): string
    {
        switch ($this->status) {
            case JobOfferStatus::New:
                return 'Laukiama patvirtinimo';

            case JobOfferStatus::Accepted:
                return 'Priimtas';

            case JobOfferStatus::Declined:
                return 'Atmestas';

            case JobOfferStatus::Closed:
                return 'U??darytas';

            default:
                return 'Ne??inomas';
        }
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return JobOffer
     */
    public function setUser(User $user): JobOffer
    {
        $this->user = $user;
        return $this;
    }

    public function canBeEditedByBuyer(): bool
    {
        return in_array($this->getStatus(), [JobOfferStatus::New], true);
    }
}
