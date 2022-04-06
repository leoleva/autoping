<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class JobPhotoDTO
{
    public function __construct(
        private string $text,
        private ?string $uuid,
        private UploadedFile $uploadedFile,
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): JobPhotoDTO
    {
        $this->text = $text;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): JobPhotoDTO
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(UploadedFile $uploadedFile): JobPhotoDTO
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }
}
