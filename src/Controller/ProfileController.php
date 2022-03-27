<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Services\User as UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private CountryRepository $countryRepository,
        private AddressRepository $addressRepository,
        private StateRepository $stateRepository,
        private CityRepository $cityRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserService $userService,
    ) {
    }

    #[Route(path: '/profile/me', name: 'my_profile')]
    public function myProfile(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->getMethod() === 'POST') {
            $newAddress = new Address();
            $newAddress->setCountryId($request->get('country'));
            $newAddress->setCityId($request->get('city'));
            $newAddress->setStateId($request->get('state'));

            $updateParams = $this->userService->updateUser(
                $this->getUser()->getId(),
                $request->get('email'),
                $request->get('password'),
                $newAddress
            );
        } else {
            $updateParams = [];
        }

        $userAddress = $this->addressRepository->findOneBy(['id' => $this->getUser()->getAddressId()]);

        if ($userAddress !== null) {
            $userCountry = $this->countryRepository->findOneBy(['id' => $userAddress->getCountryId()])?->getId() ?? 0;
            $userState = $this->stateRepository->findOneBy(['id' => $userAddress->getStateId()])?->getId() ?? 0;
            $userCity = $this->cityRepository->findOneBy(['id' => $userAddress->getCityId()])?->getId() ?? 0;

            $userCountriesStates = $this->stateRepository->findBy(['id' => $userAddress->getStateId()]) ?? [];
            $userCountryCities = $this->cityRepository->findBy(['id' => $userAddress->getCityId()]) ?? [];
        } else {
            $userCountry = 0;
            $userState = 0;
            $userCity = 0;
            $userCountriesStates = [];
            $userCountryCities = [];
        }

        $responseParams = [
            'countries' => $this->countryRepository->findAll(),
            'user_country' => $userCountry,
            'user_state' => $userState,
            'user_city' => $userCity,
            'user_country_states' => $userCountriesStates,
            'user_country_cities' => $userCountryCities,
        ];

        return $this->render('profile/my_profile.html.twig', array_merge($responseParams, $updateParams));
    }
}
