<?php

namespace App\Entity;

use App\Enum\JobStatus;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(type: 'string', enumType: JobStatus::class)]
    private JobStatus $status;

    #[ORM\Column(type: 'integer')]
    private int $address_id;

    #[ORM\OneToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id')]
    private Address $address;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: JobOffer::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'job_id', unique: false)]
    private Collection $jobOffer;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: JobPhoto::class)]
    private $jobPhotos;

    public function __construct()
    {
        $this->jobOffer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->jobPhotos = new ArrayCollection();
    }

    public function getId(): int
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDueTo(): \DateTimeInterface
    {
        return $this->dueTo;
    }

    public function setDueTo(\DateTimeInterface $dueTo): self
    {
        $this->dueTo = $dueTo;

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

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return Job
     */
    public function setAmount(string $amount): Job
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Job
     */
    public function setCurrency(string $currency): Job
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

    public function getLithuanianStatusNaming(): string
    {
        switch ($this->status) {
            case JobStatus::New:
                return 'Laukiama specialisto pasiūlymo';
            case JobStatus::Pending:
                return 'Laukiama pasiūlymo patvirtinimo';
            case JobStatus::Active:
                return 'Laukiam duomenų';
            case JobStatus::Waiting_for_review:
                return 'Laukiama užsakovo patvirtinimo';
            case JobStatus::Waiting_for_payment:
                return 'Laukiama apmokėjimo';
            case JobStatus::Done:
                return 'Atliktas';
            case JobStatus::Closed:
                return 'Uždarytas';
            default:
                return 'Nežinomas';
        }
    }

    public function getAddressId(): int
    {
        return $this->address_id;
    }

    public function setAddressId(int $address_id): self
    {
        $this->address_id = $address_id;

        return $this;
    }

    public function getAddress(): \App\Entity\Address
    {
        return $this->address;
    }

    public function setAddress(\App\Entity\Address $address): Job
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return JobOffer[]
     */
    public function getJobOffer(): Collection
    {
        return $this->jobOffer;
    }

    public function setJobOffer(Collection $jobOffer): Job
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    /**
     * @return JobPhoto[]|Collection<int, JobPhoto>
     */
    public function getJobPhotos(): Collection
    {
        return $this->jobPhotos;
    }

    public function addJobPhoto(JobPhoto $jobPhoto): self
    {
        if (!$this->jobPhotos->contains($jobPhoto)) {
            $this->jobPhotos[] = $jobPhoto;
            $jobPhoto->setJob($this);
        }

        return $this;
    }

    public function removeJobPhoto(JobPhoto $jobPhoto): self
    {
        if ($this->jobPhotos->removeElement($jobPhoto)) {
            // set the owning side to null (unless already changed)
            if ($jobPhoto->getJob() === $this) {
                $jobPhoto->setJob(null);
            }
        }

        return $this;
    }
}
