<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\UserReview;
use App\Repository\JobRepository;
use App\Repository\UserReviewRepository;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class JobExtension extends AbstractExtension
{
    public function __construct(
        private UserReviewRepository $userReviewRepository,
        private JobRepository $jobRepository
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_user_review_by_user_id_and_job_id', [$this, 'getUserReviewByUserIdAndJobId']),
            new TwigFunction('get_count_of_created_jobs', [$this, 'get_count_of_created_jobs']),
            new TwigFunction('get_count_of_executed_jobs', [$this, 'get_count_of_executed_jobs']),
            new TwigFunction('time_elapsed_string', [$this, 'time_elapsed_string']),
        ];
    }

    public function getUserReviewByUserIdAndJobId(int $userId, int $jobId): ?UserReview
    {
        return $this->userReviewRepository->findOneBy(['reviewer_id' => $userId, 'jobId' => $jobId]);
    }

    public function get_count_of_created_jobs(int $userId): int
    {
        return $this->jobRepository->count(['user_id' => $userId]);
    }

    public function get_count_of_executed_jobs(int $userId): int
    {
        return count($this->jobRepository->getAssignedJobs($userId));
    }

    public function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'met',
            'm' => 'mėn',
            'w' => 'sav',
            'd' => 'dien',
            'h' => 'val',
            'i' => 'min',
            's' => 'sek',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '.' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' prieš' : 'ką tik';
    }
}
