<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route(path: '', name: 'get_countries')]
    public function getCountries(CountryRepository $countryRepository): Response
    {
        return $this->json($countryRepository->findAll());
    }
}
