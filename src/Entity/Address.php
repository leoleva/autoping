<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cityId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stateId;

    #[ORM\Column(name: 'country_id', type: 'integer')]
    private int $countryId;

    #[ORM\OneToOne(targetEntity: Country::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private Country $country;

    #[ORM\OneToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id', nullable: true)]
    private ?City $city;

    #[ORM\OneToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id', nullable: true)]
    private ?State $state;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Address
     */
    public function setId(int $id): Address
    {
        $this->id = $id;
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
     * @return Address
     */
    public function setCityId(?int $cityId): Address
    {
        $this->cityId = $cityId;
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
     * @return Address
     */
    public function setStateId(?int $stateId): Address
    {
        $this->stateId = $stateId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     * @return Address
     */
    public function setCountryId(int $countryId): Address
    {
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     * @return Address
     */
    public function setCity(?City $city): Address
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Address
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return State|null
     */
    public function getState(): ?State
    {
        return $this->state;
    }

    /**
     * @param State|null $state
     * @return Address
     */
    public function setState(?State $state): Address
    {
        $this->state = $state;
        return $this;
    }


}
