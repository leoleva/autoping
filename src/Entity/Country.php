<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 3)]
    private string $iso3166;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Country
     */
    public function setName(string $name): Country
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getIso3166(): string
    {
        return $this->iso3166;
    }

    /**
     * @param string $iso3166
     * @return Country
     */
    public function setIso3166(string $iso3166): Country
    {
        $this->iso3166 = $iso3166;
        return $this;
    }
}
