<?php

declare(strict_types=1);

namespace App\Controller\JobOffer;

use App\Controller\AbstractController;
use App\Enum\JobOfferStatus;
use App\Repository\JobOfferRepository;
use App\Repository\JobRepository;
use App\Services\JobOfferCreator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobOfferSpecialistController extends AbstractController
{
    public function __construct(
        private JobOfferRepository $jobOfferRepository,
        private EntityManagerInterface $entityManager,
        private JobRepository $jobRepository,
        private JobOfferCreator $jobOfferCreator,
    ) {
    }

    #[Route(path: '/job-offer/specialist/my-offers', name: 'specialist_offers')]
    public function specialistOffers(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('joboffer/specialist_offers.html.twig', [
            'offers' => $this->jobOfferRepository->findBy(['user_id' => $this->getUser()->getId()], ['id' => 'desc']),
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

        $this->addFlash('specialist_offer_success', 'Pasiūlymas pašalintas sėkmingai');

        return $this->redirectToRoute('specialist_offers');
    }


    #[Route(path: '/job/{id}/job-offer/specialist/send-offer', name: 'send_offer_view')]
    public function sendOfferView(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        return $this->render('joboffer/send_job_offer.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route(path: '/job/{id}/job-offer/specialist/send-offer/handle', name: 'send_offer_handle')]
    public function sendOfferHandle(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        $this->jobOfferCreator->createJobOffer(
            $id,
            $this->getUser(),
            $request->request->get('text'),
            new DateTime(),
            new Money($request->request->get('amount'), $job->getCurrency()),
            JobOfferStatus::New,
        );

        $this->addFlash('specialist_offer_success', 'Pasiūlymas išsiųstas sėkmingai');

        return $this->redirectToRoute('specialist_offers');
    }

    #[Route(path: '/job/{id}/job-offer/create/default', name: 'create_default_job_offer')]
    public function createDefaultJobOffer(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        $this->jobOfferCreator->createJobOffer(
            $id,
            $this->getUser(),
            'Sutinku su jūsų pirminiu pasiūlymu',
            new DateTime(),
            new Money($job->getAmount(), $job->getCurrency()),
            JobOfferStatus::New,
        );

        $this->addFlash('specialist_offer_success', 'Pasiūlymas išsiųstas sėkmingai');

        return $this->redirectToRoute('specialist_offers');
    }
}
