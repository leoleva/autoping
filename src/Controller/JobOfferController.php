<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\JobOfferRepository;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobOfferController extends AbstractController
{
    public function __construct(
        private JobOfferRepository $jobOfferRepository,
        private EntityManagerInterface $entityManager,
        private JobRepository $jobRepository
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

    #[Route(path: '/job/{id}/job-offer/specialist/send-offer', name: 'send_offer')]
    public function sendOffer(int $id, Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $job = $this->jobRepository->getById($id);

        return $this->render('joboffer/specialist_offers.html.twig', [
            'offers' => $this->jobOfferRepository->findBy(['user_id' => $this->getUser()->getId()]),
        ]);
    }
}
