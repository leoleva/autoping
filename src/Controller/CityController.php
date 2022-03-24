<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    #[Route(path: '/countries/{countryId}/cities', name: 'get_cities_by_country_id')]
    public function getCitiesByCountryId(int $countryId, CityRepository $cityRepository): Response
    {
        $json = [];

        foreach ($cityRepository->findBy(['country_id' => $countryId]) as $city) {
            $json[$city->getId()] = $city->getName();
        }

        return $this->json($json);
    }
}
