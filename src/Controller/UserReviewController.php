<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Country;
use App\Entity\JobOffer;
use App\Entity\User;
use App\Entity\UserReview;
use App\Enum\JobOfferStatus;
use App\Enum\UserType;
use App\Repository\JobRepository;
use App\Repository\UserReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserReviewController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
        private EntityManagerInterface $entityManager,
        private UserReviewRepository $userReviewRepository
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

        return $this->render('user-review/add_review.html.twig', [
            'job' => $job,
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
            return $this->redirectToRoute('specialist_job_list');
        }

        if ($job->getUserId() !== $this->getUser()->getId() || $acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('specialist_job_list');
        }

        return $this->render('user-review/add_review.html.twig', [
            'job' => $job,
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

        if ($job->getUserId() !== $this->getUser()->getId() || $acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('specialist_job_list');
        }

        $userReviews = new UserReview();
        $userReviews->setRating((int)$request->request->get('rating'));
        $userReviews->setReview($request->request->get('text'));
        $userReviews->setReviewer($this->getUser());
        $userReviews->setReviewerId($this->getUser()->getId());
        if ($this->getUser()->getUserType() === UserType::Buyer) {
            $userReviews->setUser($acceptedOffer->getUser());
        } else {
            $userReviews->setUser($this->entityManager->getReference(User::class, $job->getUserId()));
        }
        $userReviews->setCreatedAt(new \DateTime());
        $userReviews->setJobId($job->getId());

        $this->entityManager->persist($userReviews);
        $this->entityManager->flush();

        if ($this->getUser()->getUserType() === UserType::Buyer) {
            $this->addFlash('author_job_list_success', 'Atsiliepimas sėkmingai pridėtas');

            return $this->redirectToRoute('author_job_list');
        } else {
            $this->addFlash('success_specialist_job_list', 'Atsiliepimas sėkmingai pridėtas');

            return $this->redirectToRoute('specialist_job_list');
        }
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
            return $this->redirectToRoute('specialist_job_list');
        }

        if ($job->getUserId() !== $this->getUser()->getId() || $acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('job_list');
        }

        return $this->render('user-review/edit_review.html.twig', [
            'job' => $job,
            'user_review' => $userReview,
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

        $userReview->setRating((int)$request->request->get('rating'));
        $userReview->setReview($request->request->get('text'));

        $this->entityManager->persist($userReview);
        $this->entityManager->flush();

        $this->addFlash('author_job_list_success', 'Atsiliepimas sėkmingai atnaujintas');

        return $this->redirectToRoute('author_job_list');
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

        if ($job->getUserId() !== $this->getUser()->getId() || $acceptedOffer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('job_list');
        }

        $this->entityManager->remove($userReview);
        $this->entityManager->flush();

        $this->addFlash('author_job_list_success', 'Atsiliepimas sėkmingai pašalintas');

        return $this->redirectToRoute('author_job_list');
    }
}
