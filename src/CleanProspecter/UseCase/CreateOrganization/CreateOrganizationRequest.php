<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

final class CreateOrganizationRequest
{
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

    /**
     * CreateOrganizationRequest constructor.
     * @param string $email
     * @param string $language
     * @param string $corporateName
     * @param string $form
     * @param string $street
     * @param string $postalCode
     * @param string $city
     * @param string $country
     * @param string $holdBy
     */
    public function __construct(string $email, string $language, string $corporateName, string $form, string $street, string $postalCode, string $city, string $country, $holdBy)
    {
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getCorporateName(): string
    {
        return $this->corporateName;
    }

    /**
     * @return string
     */
    public function getForm(): string
    {
        return $this->form;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getHoldBy()
    {
        return $this->holdBy;
    }
}
