<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

use SplFileInfo;

final class CreateOrganizationRequest
{
    /**
     * @var mixed
     */
    private $ownedBy;
    /**
     * @var string
     */
    private $phoneNumber;
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
    private $type;
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
     * @var string
     */
    private $observations;
    /**
     * @var SplFileInfo
     */
    private $logo;
    /**
     * @var mixed
     */
    private $holdBy;

    public function __construct(
        $ownedBy,
        ?string $phoneNumber,
        ?string $email,
        ?string $language,
        ?string $corporateName,
        ?string $type,
        ?string $form,
        ?string $street,
        ?string $postalCode,
        ?string $city,
        ?string $country,
        ?string $observations,
        ?SplFileInfo $logo,
        $holdBy
    ) {
        $this->ownedBy = $ownedBy;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->language = $language;
        $this->corporateName = $corporateName;
        $this->form = $form;
        $this->type = $type;
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
        $this->observations = $observations;
        $this->logo = $logo;
        $this->holdBy = $holdBy;
    }

    /**
     * @return mixed
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
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

    public function getType(): ?string
    {
        return $this->type;
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

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function getLogo(): ?SplFileInfo
    {
        return $this->logo;
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
