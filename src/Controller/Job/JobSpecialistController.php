<?php

declare(strict_types=1);

namespace App\Controller\Job;

use App\Controller\AbstractController;
use App\Enum\JobStatus;
use App\Repository\JobRepository;
use App\Services\Job\JobUpdater;
use App\Services\JobOffer\AcceptedOfferValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobSpecialistController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
        private EntityManagerInterface $entityManager,
        private AcceptedOfferValidator $acceptedOfferValidator,
        private JobUpdater $jobUpdater
    ) {
    }

    #[Route(path: '/job/{id}/submit-for-review', name: 'submit_job_for_review')]
    public function submitJobForReview(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if (!$this->acceptedOfferValidator->isUserJobAssigneeByJobAndUser($job, $this->getUser())) {
            return $this->redirectToRoute('specialist_job_list');
        }

        $job->setStatus(JobStatus::Waiting_for_review);

        $this->jobUpdater->updateJobStatus($id, JobStatus::Waiting_for_review);

        $this->addFlash('success_specialist_job_list', 'Darbas sėkmingai pateiktas peržiūrai');

        return $this->redirectToRoute('specialist_job_list');
    }

    #[Route(path: '/job/specialist/{id}/close', name: 'specialist_close_job')]
    public function declineJob(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if (!$this->acceptedOfferValidator->isUserJobAssigneeByJobAndUser($job, $this->getUser())) {
            return $this->redirectToRoute('specialist_job_list');
        }

        $this->jobUpdater->updateJobStatus($id, JobStatus::Closed);

        $this->addFlash('success_specialist_job_list', 'Darbo sėkmingai atsisakyta');

        return $this->redirectToRoute('specialist_job_list');
    }
}
