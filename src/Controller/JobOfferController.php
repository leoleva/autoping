<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\JobOfferStatus;
use App\Enum\JobStatus;
use App\Repository\JobOfferRepository;
use App\Repository\JobRepository;
use App\Services\JobOffer;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobOfferController extends AbstractController
{
    public function __construct(
        private JobOfferRepository $jobOfferRepository,
        private EntityManagerInterface $entityManager,
        private JobRepository $jobRepository,
        private JobOffer $jobOffer
    ) {
    }

    #[Route(path: '/job-offer/specialist/my-offers', name: 'specialist_offers')]
    public function specialistOffers(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('joboffer/specialist_offers.html.twig', [
            'offers' => $this->jobOfferRepository->findBy(['user_id' => $this->getUser()->getId()]),
        ]);
    }

    #[Route(path: '/job-offer/specialist/{id}/delete', name: 'specialist_delete_offer')]
    public function deleteJobOffer(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $offer = $this->jobOfferRepository->getById($id);

        if ($offer->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('specialist_offers');
        }

        $this->entityManager->remove($offer);
        $this->entityManager->flush();

        $this->addFlash('offer_removed', 'Pasiūlymas pašalintas sėkmingai');

        return $this->redirectToRoute('specialist_offers');
    }

    #[Route(path: '/job-offer/{id}/decline', name: 'decline_job_offer')]
    public function declineJobOffer(int $id, Request $request): Response
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

        $this->addFlash('specialist_offer_removed', 'Pasiūlymas atmestas sėkmingai');

        return $this->redirectToRoute('author_job_offer_list', ['id' => $offer->getJob()->getId()]);
    }

    #[Route(path: '/job/{id}/job-offer/specialist/send-offer', name: 'send_offer_view')]
    public function sendOffer(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        return $this->render('joboffer/send_job_offer.html.twig', [
            'job' => $job,
            'offers' => $this->jobOfferRepository->findBy(['user_id' => $this->getUser()->getId()]),
        ]);
    }

    #[Route(path: '/job/{id}/job-offer/specialist/send-offer/handle', name: 'send_offer_handle')]
    public function sendOfferHandle(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        $this->jobOffer->createJobOffer(
            $id,
            $this->getUser(),
            $request->request->get('text'),
            new \DateTime(),
            new Money($request->request->get('amount'), $job->getCurrency()),
            JobOfferStatus::New,
        );

        $this->addFlash('offer_created', 'Pasiūlymas pridėtas sėkmingai');

        return $this->redirectToRoute('specialist_offers'); //todo: redirect to offer list
    }

    #[Route(path: '/job/{id}/job-offer/create/default', name: 'create_default_job_offer')]
    public function createDefaultJobOffer(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        $this->jobOffer->createJobOffer(
            $id,
            $this->getUser(),
            '',
            new \DateTime(),
            new Money($job->getAmount(), $job->getCurrency()),
            JobOfferStatus::New,
        );

        return $this->redirectToRoute('specialist_offers'); //todo: redirect to offer list
    }

    #[Route(path: '/job-offer/{id}/accept', name: 'accept_job_offer')]
    public function acceptJobOffer(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $offer = $this->jobOfferRepository->getById($id);
        $job = $offer->getJob();

        if ($offer->getJob()->getUserId() !== $this->getUser()->getId()) {
            return $this->redirectToRoute('author_job_list');
        }

        $otherOffers = $this->jobOfferRepository->findBy(['job_id' => $job->getId()]);

        foreach ($otherOffers as $otherOffer) {
            if ($otherOffer->getId() === $offer->getId()) {
                continue;
            }

            $otherOffer->setStatus(JobOfferStatus::Declined);
        }

        $offer->setStatus(JobOfferStatus::Accepted);
        $job->setStatus(JobStatus::Active);

        $this->entityManager->persist($offer);
        $this->entityManager->persist($job);

        $this->entityManager->flush();

        $this->addFlash('offer_accepted', 'Pasiūlymas sėkmingai priimtas');

        return $this->redirectToRoute('author_job_list');
    }
}
