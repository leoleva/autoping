<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobClientController
{
    public function __construct(
        private JobRepository $jobRepository,
    ){

    }

    #[Route(path: '/job/{id}/job-photo/confirm', name: 'job_list')]
    public function confirmJobPhotos(): Response
    {

    }
}
