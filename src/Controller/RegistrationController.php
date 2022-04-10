<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use App\Services\AddressHandler;
use App\Services\UserCreator;
use App\Validator\UserRequestValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private CountryRepository $countryRepository,
        private AddressHandler $addressHandler,
        private UserRequestValidator $userRequestValidator,
        private UserCreator $userCreator
    ) {
    }

    #[Route('/register', name: 'register', methods: 'GET|POST')]
    public function register(
        Request $request,
    ): Response {
        $responseParams = [
            'countries' => $this->countryRepository->findAll(),
        ];

        $errors = $this->userRequestValidator->validateCreationRequest($request);

        if ($request->getMethod() === 'POST') {
            if (count($errors) === 0) {
                $address = $this->addressHandler->persistAddress(
                    $this->addressHandler->resolveAddressFromRequest($request)
                );

                $this->userCreator->createUser(
                    $request->request->get('type'),
                    $request->request->get('email'),
                    $request->request->get('password'),
                    $address,
                    $request->request->get('account'),
                    $request->request->get('name')
                );

                $responseParams['registration_successful'] = true;
            } else {
                $responseParams['registration_errors'] = $errors;
            }
        }

        return $this->render('registration/register.html.twig', $responseParams);
    }
}
