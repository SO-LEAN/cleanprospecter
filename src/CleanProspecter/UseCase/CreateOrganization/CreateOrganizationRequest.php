<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

final class CreateOrganizationRequest
{
    /**
     * @var mixed
     */
    private $ownedBy;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $language;
    /**
     * @var string
     */
    private $corporateName;
    /**
     * @var string
     */
    private $form;
    /**
     * @var string
     */
    private $street;
    /**
     * @var string
     */
    private $postalCode;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $country;
    /**
     * @var mixed
     */
    private $holdBy;

    public function __construct($ownedBy, ?string $email, ?string $language, ?string $corporateName, ?string $form, ?string $street, ?string $postalCode, ?string $city, ?string $country, $holdBy)
    {
        $this->ownedBy = $ownedBy;
        $this->email = $email;
        $this->language = $language;
        $this->corporateName = $corporateName;
        $this->form = $form;
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
        $this->holdBy = $holdBy;
    }

    /**
     * @return mixed
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getHoldBy()
    {
        return $this->holdBy;
    }

    public function hasAddress()
    {
        return !(null === $this->street &&  null === $this->postalCode && null === $this->city && null == $this->country);
    }
}
