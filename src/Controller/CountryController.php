<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    #[Route(path: '/countries', name: 'get_countries')]
    public function getCountries(CountryRepository $countryRepository): Response
    {
        $json = [];

        foreach ($countryRepository->findAll() as $country) {
            $json[$country->getId()] = $country->getName();
        }

        return $this->json($json);
    }
}
