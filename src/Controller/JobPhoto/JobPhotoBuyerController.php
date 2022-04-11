<?php

declare(strict_types=1);

namespace App\Controller\JobPhoto;

use App\Controller\AbstractController;
use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobPhotoBuyerController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository
    ) {
    }

    #[Route(path: '/job/{id}/job-photo/review', name: 'review_job_photos')]
    public function reviewJobPhotos(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if ($job->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        return $this->render('job-photo/review_job_photos.html.twig', [
            'job' => $job,
            'job_photos' => $job->getJobPhotos(),
        ]);
    }
}
