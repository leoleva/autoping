<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\JobOffer;
use App\Enum\JobOfferStatus;
use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobPhotoController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
    ) {
    }

    #[Route(path: '/job/{id}/job-photo/add', name: 'add_job_photos')]
    public function addJobPhotos(int $id, Request $request): Response
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

        ]);
    }
}
