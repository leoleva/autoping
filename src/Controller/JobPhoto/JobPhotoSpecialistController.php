<?php

declare(strict_types=1);

namespace App\Controller\JobPhoto;

use App\Controller\AbstractController;
use App\Entity\JobOffer;
use App\Enum\JobOfferStatus;
use App\Repository\JobRepository;
use App\Services\JobOffer\AcceptedOfferValidator;
use App\Services\JobPhoto\JobPhotoCreator;
use App\Services\JobPhoto\JobPhotoRemover;
use App\Services\JobPhoto\JobPhotoUpdater;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobPhotoSpecialistController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
        private AcceptedOfferValidator $acceptedOfferValidator,
        private JobPhotoCreator $jobPhotoCreator,
        private JobPhotoUpdater $jobPhotoUpdater,
        private JobPhotoRemover $jobPhotoRemover
    ) {
    }

    #[Route(path: '/job/{id}/job-photo/add', name: 'add_job_photos_view')]
    public function addJobPhotosView(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if (!$this->acceptedOfferValidator->isUserJobAssigneeByJobAndUser($job, $this->getUser())) {
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

        if (!$this->acceptedOfferValidator->isUserJobAssigneeByJobAndUser($job, $this->getUser())) {
            return $this->redirectToRoute('specialist_job_list');
        }

        $this->addFlash('success_job_photo_edit', 'Informacija sėkmingai įkelta');

        $this->jobPhotoCreator->createJobFromRequest($job, $request);

        return $this->redirectToRoute('edit_job_photos_view', ['id' => $job->getId()]);
    }

    #[Route(path: '/job/{id}/job-photo/edit', name: 'edit_job_photos_view')]
    public function editJobPhotosView(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if (!$this->acceptedOfferValidator->isUserJobAssigneeByJobAndUser($job, $this->getUser())) {
            return $this->redirectToRoute('specialist_job_list');
        }

        return $this->render('job-photo/edit_job_photos.html.twig', [
            'job' => $job,
            'job_photos' => $job->getJobPhotos(),
        ]);
    }

    #[Route(path: '/job/{id}/job-photo/edit/handle', name: 'edit_job_photos_handle')]
    public function editJobPhotosHandle(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if (!$this->acceptedOfferValidator->isUserJobAssigneeByJobAndUser($job, $this->getUser())) {
            return $this->redirectToRoute('specialist_job_list');
        }

        $this->jobPhotoUpdater->updateFromRequest($job, $request);

        $this->addFlash('success_job_photo_edit', 'Informacija atnaujinta sėkmingai');

        return $this->redirectToRoute('edit_job_photos_view', ['id' => $id]);
    }

    #[Route(path: '/job-photo/{id}/delete', name: 'delete_job_photo', methods: 'POST')]
    public function deleteJobPhoto(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->json('', 400);
        }

        $this->jobPhotoRemover->removeByIdAndUser($id, $this->getUser());

        return $this->json(['ok'], 200);
    }


    #[Route(path: '/job/{id}/job-photo/view', name: 'view_job_photos')]
    public function viewJobPhotos(int $id): Response
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

        return $this->render('job-photo/view_job_photos.html.twig', [
            'job' => $job,
            'job_photos' => $job->getJobPhotos(),
        ]);
    }
}
