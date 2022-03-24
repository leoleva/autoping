<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile/me', name: 'my_profile')]
    public function myProfile(): Response
    {
        return $this->render('profile/my_profile.html.twig');
    }
}
