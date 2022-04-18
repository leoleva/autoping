<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository
    ) {
    }

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $jobs = $this->jobRepository->getLast3Jobs();

        $maxAmount = 0;

        foreach ($jobs as $job) {
            if ($job->getAmount() > $maxAmount) {
                $maxAmount = $job->getAmount();
            }
        }

        return $this->render('index/index.html.twig', [
            'jobs' => $jobs,
            'max_amount' => $maxAmount,
        ]);
    }
}
