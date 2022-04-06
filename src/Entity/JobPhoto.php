<?php

namespace App\Entity;

use App\Repository\JobPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobPhotoRepository::class)]
class JobPhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'jobPhotos')]
    #[ORM\JoinColumn(nullable: false)]
    private Job $job;

    #[ORM\Column(type: 'string', length: 255)]
    private string $photo;

    #[ORM\Column(type: 'text')]
    private string $comment;

    #[ORM\Column(type: 'text', nullable: false, unique: true)]
    private string $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): JobPhoto
    {
        $this->uuid = $uuid;

        return $this;
    }
}
