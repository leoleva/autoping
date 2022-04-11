<?php

declare(strict_types=1);

namespace App\Services\JobPhoto;

use App\Entity\Job;
use App\Entity\JobPhoto;
use App\Repository\JobPhotoRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class JobPhotoUpdater
{
    public function __construct(
        private JobPhotoRepository $jobPhotoRepository,
        private EntityManagerInterface $entityManager,
        private FileUploader $fileUploader
    ) {

    }

    public function updateFromRequest(Job $job, Request $request): void
    {
        foreach ($request->request->all('existing_text') as $id => $text) {
            $jobPhoto = $this->jobPhotoRepository->getById($id);
            $jobPhoto->setComment($text);

            $this->entityManager->persist($jobPhoto);
        }

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
