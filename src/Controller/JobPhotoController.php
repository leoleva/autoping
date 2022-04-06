<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\JobPhoto;
use App\Enum\JobOfferStatus;
use App\Repository\JobPhotoRepository;
use App\Repository\JobRepository;
use App\Services\FileUploader;
use App\Services\JobPhotoResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobPhotoController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
        private FileUploader $fileUploader,
        private EntityManagerInterface $entityManager,
        private JobPhotoRepository $jobPhotoRepository
    ) {
    }

    #[Route(path: '/job/{id}/job-photo/add', name: 'add_job_photos_view')]
    public function addJobPhotosView(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute('specialist_job_list');
        }

        if ($acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('specialist_job_list');
        }

        return $this->render('job-photo/add_job_photos.html.twig', [
            'job' => $job,
            'job_photos' => $job->getJobPhotos(),
        ]);
    }

    #[Route(path: '/job/{id}/job-photo/add/handle', name: 'add_job_photos_handle')]
    public function addJobPhotosHandle(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute('specialist_job_list');
        }

        if ($acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('specialist_job_list');
        }

        /** @var UploadedFile[] $files */
        $files = $request->files->all('file');

        foreach ($files as $key => $file) {
            $fileName = $this->fileUploader->upload($file);

            $text = $request->request->get('text')[$key] ?? '';
            $uuid = $request->request->get('uuid')[$key] ?? null;

            $jobPhoto = new JobPhoto();
            $jobPhoto->setComment($text);
            $jobPhoto->setPhoto($fileName);
            $jobPhoto->setJob($job);
            $jobPhoto->setUuid(bin2hex(random_bytes(50)));

            $this->entityManager->persist($jobPhoto);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('specialist_job_list');
    }

    #[Route(path: '/job/{id}/job-photo/edit', name: 'edit_job_photos_view')]
    public function editJobPhotosView(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute('specialist_job_list');
        }

        if ($acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('specialist_job_list');
        }

        return $this->render('job-photo/edit_job_photos.html.twig', [
            'job' => $job,
            'job_photos' => $job->getJobPhotos(),
        ]);
    }

    #[Route(path: '/job-photo/{id}/delete', name: 'delete_job_photo', methods: 'POST')]
    public function deleteJobPhoto(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->json('', 400);
        }

        $jobPhoto = $this->jobPhotoRepository->getById($id);
        $job = $jobPhoto->getJob();

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->json(['užsakymas nepatvirtintas'], 400);
        }

        if ($acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->json(['jus nesate užsakymo vykdytojas'], 400);
        }

        $this->entityManager->remove($jobPhoto);
        $this->entityManager->flush();

        return $this->json(['ok'], 200);
    }
}
