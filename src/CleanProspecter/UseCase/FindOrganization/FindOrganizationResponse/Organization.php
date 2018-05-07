<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationResponse;

class Organization
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $logo;

    public function __construct($id, string $fullName, ?string $city, ?string $country, ?string $postalCode, ?string $logo)
    {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->city = $city;
        $this->country = $country;
        $this->postalCode = $postalCode;
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }
}
