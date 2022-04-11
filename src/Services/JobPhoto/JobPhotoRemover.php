<?php

declare(strict_types=1);

namespace App\Services\JobPhoto;

use App\Entity\User;
use App\Repository\JobPhotoRepository;
use App\Services\JobOffer\AcceptedOfferValidator;
use Doctrine\ORM\EntityManagerInterface;
use GeneralException;

class JobPhotoRemover
{
    public function __construct(
        private AcceptedOfferValidator $acceptedOfferValidator,
        private JobPhotoRepository $jobPhotoRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function removeByIdAndUser(int $id, User $user): void
    {
        $jobPhoto = $this->jobPhotoRepository->getById($id);

        if (!$this->acceptedOfferValidator->isUserJobAssigneeByJobAndUser(
            $jobPhoto->getJob(),
            $user
        )) {
            throw new GeneralException('Jūs nesate užsakymo vykdytojas');
        }

        $this->entityManager->remove($jobPhoto);
        $this->entityManager->flush();
    }
}
