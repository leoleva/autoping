<?php

namespace App\Entity;

use App\Enum\JobStatus;
use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;
use Evp\Component\Money\Money;

#[ORM\Entity(repositoryClass: JobRepository::class)]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $user_id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 10000)]
    private string $text;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dueTo;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updated_at;

    #[ORM\Column(type: 'string', length: 255)]
    private string $amount;

    #[ORM\Column(type: 'string', length: 255)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 255, enumType: JobStatus::class)]
    private JobStatus $status;

    #[ORM\Column(type: 'integer')]
    private int $address_id;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getDueTo(): ?\DateTimeInterface
    {
        return $this->dueTo;
    }

    public function setDueTo(\DateTimeInterface $dueTo): self
    {
        $this->dueTo = $dueTo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @param Money $amount
     * @return Job
     */
    public function setAmount(Money $amount): Job
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return Money
     */
    public function getCurrency(): Money
    {
        return $this->currency;
    }

    /**
     * @param Money $currency
     * @return Job
     */
    public function setCurrency(Money $currency): Job
    {
        $this->currency = $currency;
        return $this;
    }


    public function getStatus(): JobStatus
    {
        return $this->status;
    }

    public function setStatus(JobStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAddressId(): ?int
    {
        return $this->address_id;
    }

    public function setAddressId(int $address_id): self
    {
        $this->address_id = $address_id;

        return $this;
    }
}
