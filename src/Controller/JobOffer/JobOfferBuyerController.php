<?php

declare(strict_types=1);

namespace App\Controller\JobOffer;

use App\Controller\AbstractController;
use App\Enum\JobOfferStatus;
use App\Repository\JobOfferRepository;
use App\Services\JobOffer\AcceptJobOfferHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobOfferBuyerController extends AbstractController
{
    public function __construct(
        private JobOfferRepository $jobOfferRepository,
        private EntityManagerInterface $entityManager,
        private AcceptJobOfferHandler $acceptJobOfferHandler
    ) {
    }

    #[Route(path: '/job-offer/{id}/decline', name: 'decline_job_offer_handle')]
    public function declineJobOfferHandle(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $offer = $this->jobOfferRepository->getById($id);

        if ($offer->getJob()->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $offer->setStatus(JobOfferStatus::Declined);
        $this->entityManager->persist($offer);
        $this->entityManager->flush();

        $this->addFlash('author_view_job_offer_success', 'Pasiūlymas atmestas sėkmingai');

        return $this->redirectToRoute('author_view_job', ['id' => $offer->getJob()->getId()]);
    }

    #[Route(path: '/job-offer/{id}/accept', name: 'accept_job_offer')]
    public function acceptJobOffer(int $id): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $offer = $this->jobOfferRepository->getById($id);

        if ($offer->getJob()->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $this->acceptJobOfferHandler->accept($offer);

        $this->addFlash('author_view_job_success', 'Pasiūlymas sėkmingai priimtas');

        return $this->redirectToRoute('author_view_job', ['id' => $offer->getJob()->getId()]);
    }
}
