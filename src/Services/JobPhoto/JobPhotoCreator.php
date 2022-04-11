<?php

declare(strict_types=1);

namespace App\Services\JobPhoto;

use App\Entity\Job;
use App\Entity\JobPhoto;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class JobPhotoCreator
{
    public function __construct(
        private FileUploader $fileUploader,
        private EntityManagerInterface $entityManager
    ) {

    }

    public function createJobFromRequest(Job $job, Request $request): void
    {
        /** @var UploadedFile[] $files */
        $files = $request->files->all('file');

        foreach ($files as $key => $file) {
            $fileName = $this->fileUploader->upload($file);

            $text = $request->request->get('text')[$key] ?? '';

            $jobPhoto = new JobPhoto();
            $jobPhoto->setComment($text);
            $jobPhoto->setPhoto($fileName);
            $jobPhoto->setJob($job);

            $this->entityManager->persist($jobPhoto);
        }

        $this->entityManager->flush();
    }
}
