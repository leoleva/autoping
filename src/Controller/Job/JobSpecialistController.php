<?php

declare(strict_types=1);

namespace App\Controller\Job;

use App\Controller\AbstractController;
use App\Entity\JobOffer;
use App\Enum\JobOfferStatus;
use App\Enum\JobStatus;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobSpecialistController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
        private EntityManagerInterface $entityManager
    ) {

    }

    #[Route(path: '/job/{id}/submit-for-review', name: 'submit_job_for_review')]
    public function submitJobForReview(int $id): Response
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

        $job->setStatus(JobStatus::Waiting_for_review);

        $this->entityManager->persist($job);
        $this->entityManager->flush();

        $this->addFlash('success_specialist_job_list', 'Sėkmingai pateikta peržiūrai');

        return $this->redirectToRoute('specialist_job_list');
    }

    #[Route(path: '/job/specialist/{id}/close', name: 'specialist_close_job')]
    public function declineJob(int $id): Response
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

        $job->setStatus(JobStatus::Closed);

        $this->entityManager->persist($job);
        $this->entityManager->flush();

        $this->addFlash('success_specialist_job_list', 'Darbo sėkmingai atsisakyta');

        return $this->redirectToRoute('specialist_job_list');
    }
}
