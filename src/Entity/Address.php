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

    #[ORM\Column(type: 'integer', nullable: true)]
    private int $countryId;

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
}
