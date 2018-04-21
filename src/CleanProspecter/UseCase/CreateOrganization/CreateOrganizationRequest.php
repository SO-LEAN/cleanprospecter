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
    private $country;
    /**
     * @var string
     */
    private $corporateName;
    /**
     * @var string
     */
    private $form;
    /**
     * @var mixed
     */
    private $holdBy;

    /**
     * CreateOrganizationRequest constructor.
     * @param string $email
     * @param string $country
     * @param string $corporateName
     * @param string $form
     * @param string $holdBy
     */
    public function __construct(string $email, string $country, string $corporateName, string $form, $holdBy)
    {
        $this->email = $email;
        $this->country = $country;
        $this->corporateName = $corporateName;
        $this->form = $form;
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
    public function getCountry(): string
    {
        return $this->country;
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
     * @return mixed
     */
    public function getHoldBy()
    {
        return $this->holdBy;
    }
}
