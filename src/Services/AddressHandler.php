<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressHandler
{
    public function __construct(
      private EntityManagerInterface $entityManager
    ) {

    }

    public function resolveAddressFromRequest(Request $request): Address
    {
        $address = new Address();

        $countryId = filter_var($request->request->get('country_id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $stateId = filter_var($request->request->get('state_id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $cityId = filter_var($request->request->get('city_id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if ($countryId !== null) {
            $address->setCountry($this->entityManager->getReference(Country::class, $countryId));
        }

        if ($stateId !== null) {
            $address->setState($this->entityManager->getReference(State::class, $stateId));
        }

        if ($cityId !== null) {
            $address->setCity($this->entityManager->getReference(City::class, $cityId));
        }

        return $address;
    }
}
