<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Address;
use App\Enum\JobStatus;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\JobRepository;
use App\Repository\StateRepository;
use App\Services\Job;
use App\Validator\JobAddValidator;
use App\Validator\JobUpdateValidator;
use Evp\Component\Money\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    public function __construct(
        private CountryRepository $countryRepository,
        private JobAddValidator $jobAddValidator,
        private Job $job,
        private JobRepository $jobRepository,
        private StateRepository $stateRepository,
        private CityRepository $cityRepository,
        private AddressRepository $addressRepository,
        private JobUpdateValidator $jobUpdateValidator,
    ) {}

    #[Route(path: '/job/add', name: 'index')]
    public function index(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $responseParams = [
            'countries' => $this->countryRepository->findAll(),
        ];

        if ($request->getMethod() === 'POST') {
            $errors = $this->jobAddValidator->getValidationErrors($request);

            if (count($errors) === 0) {
                $address = (new Address())
                    ->setCountryId((int)$request->request->getInt('country_id'))
                    ->setCityId($request->request->get('city_id') !== null ? $request->request->getInt('city_id') : null)
                    ->setStateId($request->request->get('state_id') !== null ? $request->request->getInt('state_id') : null);

                $this->job->createNewJob(
                    $this->getUser(),
                    $request->request->get('title'),
                    $request->request->get('text'),
                    new \DateTime($request->request->get('due_to')),
                    Money::create($request->request->getInt('price'), 'EUR'),
                    JobStatus::New,
                    $address
                );

                return $this->redirectToRoute('author_job_list');
            }

            $responseParams['job_add_errors'] = $errors;
        }

        return $this->render('job/add.html.twig', $responseParams);
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

    #[Route(path: '/job/my/{id}/delete', name: 'delete_job_by_id')]
    public function deleteJob(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->jobRepository->find($id)?->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $this->job->delete($id);

        $this->addFlash('job_deleted', 'Skelbimas ištrintas sėkmingai');

        return $this->redirectToRoute('author_job_list');
    }

    #[Route(path: '/job/my/{id}/offers', name: 'author_job_offer_list')]
    public function getAuthorJobOfferList(Request $request): Response
    {
        // todo: implement

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('job/author_job_list.html.twig', [
            'jobs' => $this->jobRepository->findBy(['user_id' => $this->getUser()->getId()])
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

        $this->addFlash('job_update_errors', $errors);

        if (count($errors) === 0) {
            $address = (new Address())
                ->setCountryId((int)$request->request->getInt('country_id'))
                ->setCityId($request->request->get('city_id') !== null ? $request->request->getInt('city_id') : null)
                ->setStateId($request->request->get('state_id') !== null ? $request->request->getInt('state_id') : null);

            $this->job->updateJob(
                $id,
                $this->getUser(),
                $request->request->get('title'),
                $request->request->get('text'),
                new \DateTime($request->request->get('due_to')),
                Money::create($request->request->getInt('price'), 'EUR'),
                $address
            );

            $this->addFlash('job_update_successful', 'Skelbimas atnaujintas sėkmingai');
        }

        return $this->redirectToRoute('edit_job_view', ['id'  => $id]);
    }
}