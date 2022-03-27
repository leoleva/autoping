<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Address;
use App\Enum\JobStatus;
use App\Repository\CountryRepository;
use App\Services\Job;
use App\Validator\JobAddValidator;
use Evp\Component\Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    public function __construct(
        private CountryRepository $countryRepository,
        private JobAddValidator $jobAddValidator,
        private Job $job
    ) {

    }

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
                    ->setCountryId((int)$request->request->get('country_id'))
                    ->setCityId($request->request->get('city_id'))
                    ->setStateId($request->request->get('state_id'));

                $this->job->createNewJob(
                    $this->getUser(),
                    $request->request->get('title'),
                    $request->request->get('text'),
                    new \DateTime($request->request->get('due_to')),
                    Money::createFromMinorUnits($request->request->get('price'), 'EUR'),
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

        $responseParams = [
            'countries' => $this->countryRepository->findAll(),
        ];

        if ($request->getMethod() === 'POST') {
            $errors = $this->jobAddValidator->getValidationErrors($request);

            if (count($errors) === 0) {
                $address = (new Address())
                    ->setCountryId((int)$request->request->get('country_id'))
                    ->setCityId($request->request->get('city_id'))
                    ->setStateId($request->request->get('state_id'));

                $this->job->createNewJob(
                    $this->getUser(),
                    $request->request->get('title'),
                    $request->request->get('text'),
                    new \DateTime($request->request->get('due_to')),
                    Money::createFromMinorUnits($request->request->get('price'), 'EUR'),
                    JobStatus::New,
                    $address
                );
            } else {
                $responseParams['job_add_errors'] = $errors;
            }

            return $this->redirectToRoute('');
        }

        return $this->render('job/add.html.twig', $responseParams);
    }
}
