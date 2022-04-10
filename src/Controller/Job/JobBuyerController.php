<?php

declare(strict_types=1);

namespace App\Controller\Job;

use App\Controller\AbstractController;
use App\Entity\Address;
use App\Enum\JobStatus;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\JobRepository;
use App\Repository\StateRepository;
use App\Services\AddressHandler;
use App\Services\Job\CloseJobHandler;
use App\Services\JobCreator;
use App\Services\JobUpdater;
use App\Validator\JobAddValidator;
use App\Validator\JobUpdateValidator;
use DateTime;
use Evp\Component\Money\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobBuyerController extends AbstractController
{
    public function __construct(
        private CountryRepository $countryRepository,
        private JobAddValidator $jobAddValidator,
        private AddressHandler $addressHandler,
        private JobCreator $jobCreator,
        private JobRepository $jobRepository,
        private CityRepository $cityRepository,
        private StateRepository $stateRepository,
        private AddressRepository $addressRepository,
        private JobUpdateValidator $jobUpdateValidator,
        private JobUpdater $jobUpdater,
        private CloseJobHandler $closeJobHandler,
    ) {
    }

    #[Route(path: '/job/add', name: 'add_job_view')]
    public function addJobView(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $responseParams = [
            'countries' => $this->countryRepository->findAll(),
        ];

        return $this->render('job/add_job.html.twig', $responseParams);
    }

    #[Route(path: '/job/add/handle', name: 'add_job_handle')]
    public function addJobHandle(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $errors = $this->jobAddValidator->getValidationErrors($request);

        if (count($errors) === 0) {
            $address = $this->addressHandler->resolveAddressFromRequest($request);

            $this->jobCreator->createNewJob(
                $this->getUser(),
                $request->request->get('title'),
                $request->request->get('text'),
                new DateTime($request->request->get('due_to')),
                Money::create($request->request->getInt('price'), 'EUR'),
                JobStatus::New,
                $address
            );

            $this->addFlash('author_job_list_success', 'Skelbimas pridėtas sėkmingai');

            return $this->redirectToRoute('author_job_list');
        }

        $this->addFlash('add_job_danger', $errors);

        return $this->redirectToRoute('add_job_view');
    }

    #[Route(path: '/job/my/list', name: 'author_job_list')]
    public function getAuthorJobList(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('job/author_job_list.html.twig', [
            'jobs' => $this->jobRepository->findBy(['user_id' => $this->getUser()->getId()])
        ]);
    }

    #[Route(path: '/job/author/view/{id}', name: 'author_view_job')]
    public function authorViewJob(int $id): Response
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

        return $this->render('job/author_view_job.html.twig', [
            'job' => $job,
            'status_class' => $statusClass,
        ]);
    }

    #[Route(path: '/job/my/{id}/edit', name: 'edit_job_view')]
    public function editJobView(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if ($job->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $address = $this->addressRepository->getAddressById($job->getAddressId());

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'job_address' => $address,
            'countries' => $this->countryRepository->findAll(),
            'job_country_states' => $this->stateRepository->findBy(['country_id' => $address->getCountryId()]),
            'job_country_cities' => $this->cityRepository->findBy(['country_id' => $address->getCountryId()]),
        ]);
    }

    #[Route(path: '/job/my/{id}/edit/submit', name: 'edit_job_handle')]
    public function editJobHandle(Request $request, int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if ($job->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $errors = $this->jobUpdateValidator->getValidationErrors($request);

        if (count($errors) === 0) {
            $address = (new Address())
                ->setCountryId($request->request->getInt('country_id'))
                ->setCityId($request->request->get('city_id') !== '' ? $request->request->getInt('city_id') : null)
                ->setStateId($request->request->get('state_id') !== '' ? $request->request->getInt('state_id') : null);

            $this->jobUpdater->updateJob(
                $id,
                $request->request->get('title'),
                $request->request->get('text'),
                new DateTime($request->request->get('due_to')),
                Money::create($request->request->getInt('price'), 'EUR'),
                $address
            );

            $this->addFlash('job_update_successful', 'Skelbimas atnaujintas sėkmingai');
        } else {
            $this->addFlash('job_update_errors', $errors);
        }

        return $this->redirectToRoute('edit_job_view', ['id'  => $id]);
    }

    #[Route(path: '/job/my/{id}/close', name: 'author_close_job')]
    public function closeJob(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if ($job->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $this->closeJobHandler->close($job);

        $this->addFlash('author_view_job_success', 'Skelbimas uždarytas sėkmingai');

        return $this->redirectToRoute('author_view_job', ['id' => $job->getId()]);
    }
}
