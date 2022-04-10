<?php

declare(strict_types=1);

namespace App\Controller\Job;

use App\Controller\AbstractController;
use App\DTO\SearchFilter;
use App\Entity\JobOffer;
use App\Enum\JobOfferStatus;
use App\Enum\JobStatus;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\JobOfferRepository;
use App\Repository\JobRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    public function __construct(
        private CountryRepository $countryRepository,
        private JobRepository $jobRepository,
        private StateRepository $stateRepository,
        private CityRepository $cityRepository,
        private EntityManagerInterface $entityManager,
        private JobOfferRepository $jobOfferRepository
    ) {}

    #[Route(path: '/job/list', name: 'job_list')]
    public function jobList(Request $request): Response
    {
        $searchFilter = SearchFilter::createFromRequest($request);

        $countryStates = [];
        $countryCities = [];

        if ($searchFilter->getCountryId() !== null) {
            $countryStates = $this->stateRepository->findBy(['country_id' => $searchFilter->getCountryId()]);
            $countryCities = $this->cityRepository->findBy(['country_id' => $searchFilter->getCountryId()]);
        }

        $jobs = $this->jobRepository->getAllWithAddress($searchFilter);

        return $this->render('job/list.html.twig', [
            'jobs' => $jobs,
            'countries' => $this->countryRepository->findAll(),
            'user_country' => $searchFilter->getCountryId(),
            'user_state' => $searchFilter->getStateId(),
            'user_city' => $searchFilter->getCityId(),
            'country_states' => $countryStates,
            'country_cities' => $countryCities,
        ]);
    }

    #[Route(path: '/job/specialist/list', name: 'specialist_job_list')]
    public function specialistJobList(): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $jobs = $this->jobRepository->getSpecialistJobs($this->getUser()->getId());

        return $this->render('job/specialist_job_list.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route(path: '/job/{id}/confirm-payment', name: 'confirm_job_payment')]
    public function confirmPayment(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if ($job->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $job->setStatus(JobStatus::Done);

        $this->entityManager->persist($job);
        $this->entityManager->flush();

        return $this->redirectToRoute('author_job_list');
    }

    #[Route(path: '/job/view/{id}', name: 'view_job')]
    public function viewJob(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        $statusClass = match ($job->getStatus()) {
            JobStatus::New => 'primary',
            JobStatus::Pending => 'primary',
            JobStatus::Active => 'info',
            JobStatus::Waiting_for_payment => 'warning',
            JobStatus::Waiting_for_review => 'warning',
            JobStatus::Closed => 'danger',
            JobStatus::Done => 'success',
        };

        return $this->render('job/view_job.html.twig', [
            'job' => $job,
            'status_class' => $statusClass,
        ]);
    }
}
