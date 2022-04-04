<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;

class SearchFilter
{
    private ?int $countryId;
    private ?int $stateId;
    private ?int $cityId;

    public static function createFromRequest(Request $request)
    {
        $countryId = filter_var($request->query->get('country_id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $stateId = filter_var($request->query->get('state_id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $cityId = filter_var($request->query->get('city_id'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        return (new static())
            ->setCountryId($countryId)
            ->setCityId($cityId)
            ->setStateId($stateId);
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int|null $countryId
     * @return SearchFilter
     */
    public function setCountryId(?int $countryId): SearchFilter
    {
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    /**
     * @param int|null $stateId
     * @return SearchFilter
     */
    public function setStateId(?int $stateId): SearchFilter
    {
        $this->stateId = $stateId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCityId(): ?int
    {
        return $this->cityId;
    }

    /**
     * @param int|null $cityId
     * @return SearchFilter
     */
    public function setCityId(?int $cityId): SearchFilter
    {
        $this->cityId = $cityId;
        return $this;
    }


}
