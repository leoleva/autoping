<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Address;
use App\Entity\UserReview;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Repository\UserReviewRepository;
use App\Services\UserUpdater;
use App\Validator\UserRequestValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private CountryRepository $countryRepository,
        private AddressRepository $addressRepository,
        private StateRepository $stateRepository,
        private CityRepository $cityRepository,
        private UserRepository $userRepository,
        private UserUpdater $userUpdater,
        private UserReviewRepository $userReviewRepository,
        private UserRequestValidator $userRequestValidator,
    ) {
    }

    #[Route(path: '/profile/update', name: 'my_profile_update_view')]
    public function myProfileView(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $userAddress = $this->addressRepository->findOneBy(['id' => $this->getUser()->getAddressId()]);

        if ($userAddress !== null) {
            $userCountry = $this->countryRepository->findOneBy(['id' => $userAddress->getCountryId()])?->getId() ?? 0;
            $userState = $this->stateRepository->findOneBy(['id' => $userAddress->getStateId()])?->getId() ?? 0;
            $userCity = $this->cityRepository->findOneBy(['id' => $userAddress->getCityId()])?->getId() ?? 0;

            $userCountriesStates = $this->stateRepository->findBy(['country_id' => $userAddress->getCountryId()]) ?? [];
            $userCountryCities = $this->cityRepository->findBy(['country_id' => $userAddress->getCountryId()]) ?? [];
        } else {
            $userCountry = 0;
            $userState = 0;
            $userCity = 0;
            $userCountriesStates = [];
            $userCountryCities = [];
        }

        return $this->render('profile/my_profile_update.html.twig', [
            'countries' => $this->countryRepository->findAll(),
            'user_country' => $userCountry,
            'user_state' => $userState,
            'user_city' => $userCity,
            'user_country_states' => $userCountriesStates,
            'user_country_cities' => $userCountryCities,
        ]);
    }

    #[Route(path: '/profile/update/handle', name: 'update_my_profile_handle')]
    public function myProfileHandle(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $newAddress = new Address();
        $newAddress->setCountryId($request->request->getInt('country_id'));
        $newAddress->setCityId($request->request->getInt('city_id'));
        $newAddress->setStateId($request->request->getInt('state_id'));

        $errors = $this->userRequestValidator->validateUpdateRequest($request, $this->getUser());

        if (count($errors) === 0) {
            $this->userUpdater->updateUser(
                $this->getUser()->getId(),
                $request->get('email'),
                $request->get('password'),
                $request->request->get('account'),
                $request->request->get('name'),
                $newAddress
            );

            $this->addFlash('my_profile_update_view_success', 'Profilis sėkmingai atnaujintas');
        } else {
            $this->addFlash('my_profile_update_view_danger', $errors);
        }

        return $this->redirectToRoute('my_profile_update_view');
    }

    #[Route(path: '/profile/{id}', name: 'profile_by_id')]
    public function userProfile(int $id)
    {
        $user = $this->userRepository->getUserById($id);

        /** @var UserReview[] $reviews */
        $reviews = $this->userReviewRepository->findBy(['user_id' => $user->getId()]);

        /** @var ArrayCollection|UserReview[] $collection */
        $collection = new \Doctrine\Common\Collections\ArrayCollection($reviews);

        $total5 = $collection->filter(function (UserReview $review) {return $review->getRating() === 5;})->count();
        $total4 = $collection->filter(function (UserReview $review) {return $review->getRating() === 4;})->count();
        $total3 = $collection->filter(function (UserReview $review) {return $review->getRating() === 3;})->count();
        $total2 = $collection->filter(function (UserReview $review) {return $review->getRating() === 2;})->count();
        $total1 = $collection->filter(function (UserReview $review) {return $review->getRating() === 1;})->count();

        $totalSum = 0;

        foreach ($collection as $element) {
            $totalSum +=  $element->getRating();
        }

        $totalRating = $collection->count() !== 0 ?  $totalSum / $collection->count() : null;

        $class = null;

        switch (true) {
            case $totalRating > 4:
                $class = 'success';
                break;

            case $totalRating > 3:
                $class = 'info';
                break;

            case $totalRating > 2:
                $class = 'dark';
                break;

            case $totalRating > 1:
                $class = 'warning';
                break;

            case $totalRating === 1:
                $class = 'danger';
                break;
            default:
                $class = 'secondary';
        }

        return $this->render('profile/user_profile.html.twig', [
            'user' => $user,
            'reviews' => $reviews,
            'total_count' => $collection->count(),
            'total_5_percent' => $collection->count() !== 0 ? $total5 * 100 / $collection->count() : 0,
            'total_4_percent' => $collection->count() !== 0 ? $total4 * 100 / $collection->count() : 0,
            'total_3_percent' => $collection->count() !== 0 ? $total3 * 100 / $collection->count() : 0,
            'total_2_percent' => $collection->count() !== 0 ? $total2 * 100 / $collection->count() : 0,
            'total_1_percent' => $collection->count() !== 0 ? $total1  * 100 / $collection->count() : 0,
            'total_5' => $total5,
            'total_4' => $total4,
            'total_3' => $total3,
            'total_2' => $total2,
            'total_1' => $total1,
            'total_rating' => round($totalRating, 1),
            'total_class' => $class,
        ]);
    }
}
