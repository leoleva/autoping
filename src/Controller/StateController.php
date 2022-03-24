<?php

namespace App\Controller;

use App\Repository\CityRepository;
use App\Repository\StateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StateController extends AbstractController
{
    #[Route(path: '/countries/{countryId}/states', name: 'get_states_by_country_id')]
    public function getCitiesByCountryId(int $countryId, StateRepository $stateRepository): Response
    {
        $json = [];

        foreach ($stateRepository->findBy(['country_id' => $countryId]) as $state) {
            $json[$state->getId()] = $state->getName();
        }

        return $this->json($json);
    }
}
