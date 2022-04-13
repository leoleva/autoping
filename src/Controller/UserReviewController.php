<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\JobOffer;
use App\Enum\JobOfferStatus;
use App\Enum\UserType;
use App\Repository\JobRepository;
use App\Repository\UserReviewRepository;
use App\Services\UserReview\UserReviewHandler;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserReviewController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
        private UserReviewRepository $userReviewRepository,
        private UserReviewHandler $userReviewHandler
    ) {
    }

    #[Route(path: '/user-review/job/{id}/add/buyer', name: 'buyer_leaves_review_view')]
    public function buyerAddReview(int $id)
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        if ($job->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        /** @var JobOffer $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        return $this->render('user-review/add_review.html.twig', [
            'job' => $job,
            'user' => $acceptedOffer->getUser(),
        ]);
    }

    #[Route(path: '/user-review/job/{id}/add/specialist', name: 'specialist_leaves_review_view')]
    public function specialistAddReview(int $id)
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute('job_list');
        }

        if ($acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('job_list');
        }

        return $this->render('user-review/add_review.html.twig', [
            'job' => $job,
            'user' => $job->getUser(),
        ]);
    }

    #[Route(path: '/user-review/job/{id}/add/handle', name: 'add_review_handle')]
    public function addReview(int $id, Request $request)
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute('author_job_list');
        }

        if (!($job->getUserId() !== $this->getUser()->getId()) && !($acceptedOffer->getUserId() !== $this->getUser()->getId())) {
            return $this->redirectToRoute('specialist_job_list');
        }

        $this->userReviewHandler->createReview(
            (int)$request->request->get('rating'),
            $request->request->get('text'),
            $this->getUser(),
            $this->getUser()->getUserType() === UserType::Buyer ? $acceptedOffer->getUser() : $job->getUser(),
            new DateTime(),
            $job->getId()
        );

        $this->addFlash('edit_review_success', 'Atsiliepimas sėkmingai pridėtas');

        return $this->redirectToRoute('edit_user_review_view', ['id' => $job->getId()]);
    }

    #[Route(path: '/user-review/{id}/edit', name: 'edit_user_review_view')]
    public function editReview(int $id)
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $userReview = $this->userReviewRepository->getById($id);
        $job = $this->jobRepository->getById($userReview->getJobId());

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute($this->getUser()->getUserType() === UserType::Specialist ? 'specialist_job_list' : 'author_job_list');
        }

        if (!($job->getUserId() !== $this->getUser()->getId()) && !($acceptedOffer->getUserId() !== $this->getUser()->getId())) {
            return $this->redirectToRoute($this->getUser()->getUserType() === UserType::Specialist ? 'specialist_job_list' : 'author_job_list');
        }

        return $this->render('user-review/edit_review.html.twig', [
            'job' => $job,
            'user_review' => $userReview,
            'user' => $this->getUser()->getUserType() === UserType::Buyer ? $acceptedOffer->getUser() : $job->getUser(),
        ]);
    }

    #[Route(path: '/user-review/{id}/edit/handle', name: 'edit_review_handle')]
    public function editHandle(int $id, Request $request)
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $userReview = $this->userReviewRepository->getById($id);
        $job = $this->jobRepository->getById($userReview->getJobId());

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute('specialist_job_list');
        }

        if ($job->getUserId() !== $this->getUser()->getId() || $acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('job_list');
        }

        $this->userReviewHandler->updateReview(
            $id,
            $request->request->get('text'),
            (int)$request->request->get('rating'),
        );

        $this->addFlash('edit_review_success', 'Atsiliepimas sėkmingai atnaujintas');

        return $this->redirectToRoute('edit_user_review_view', ['id' => $userReview->getId()]);
    }

    #[Route(path: '/user-review/{id}/delete', name: 'delete_review_handle')]
    public function userReviewDelete(int $id, Request $request)
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $userReview = $this->userReviewRepository->getById($id);
        $job = $this->jobRepository->getById($userReview->getJobId());

        /** @var JobOffer|false $acceptedOffer */
        $acceptedOffer = $job->getJobOffer()->filter(fn(JobOffer $jobOffer) => $jobOffer->getStatus() === JobOfferStatus::Accepted)->first();

        if (!$acceptedOffer instanceof JobOffer) {
            return $this->redirectToRoute('specialist_job_list');
        }

        if (!($job->getUserId() !== $this->getUser()->getId()) && !($acceptedOffer->getUserId() !== $this->getUser()->getId())) {
            return $this->redirectToRoute('job_list');
        }

        $this->userReviewHandler->remove($id);

        if ($this->getUser()->getUserType() === UserType::Buyer) {
            $this->addFlash('author_job_list_success', 'Atsiliepimas sėkmingai pašalintas');

            return $this->redirectToRoute('author_job_list');
        } else {
            $this->addFlash('success_specialist_job_list', 'Atsiliepimas sėkmingai pašalintas');

            return $this->redirectToRoute('specialist_job_list');
        }
    }
}
