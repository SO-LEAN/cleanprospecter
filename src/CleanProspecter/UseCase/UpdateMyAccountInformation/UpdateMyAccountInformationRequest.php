<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateMyAccountInformation;

use SplFileInfo;

final class UpdateMyAccountInformationRequest
{
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
    private $userName;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var SplFileInfo
     */
    private $picture;
    /**
     * @var string
     */
    private $organizationCorporateName;
    /**
     * @var string
     */
    private $organizationForm;
    /**
     * @var SplFileInfo
     */
    private $organizationLogo;

    public function __construct(
        string $userName,
        ?string $password,
        ?string $firstName,
        ?string $lastName,
        ?SplFileInfo $picture,
        ?string $phoneNumber,
        ?string $email,
        ?string $language,
        ?string $organizationCorporateName,
        ?string $organizationForm,
        ?SplFileInfo $organizationLogo
    ) {
        $this->userName = $userName;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->picture = $picture;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->language = $language;
        $this->organizationCorporateName = $organizationCorporateName;
        $this->organizationForm = $organizationForm;
        $this->organizationLogo = $organizationLogo;
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

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPicture(): ?SplFileInfo
    {
        return $this->picture;
    }

    public function getOrganizationCorporateName(): string
    {
        return $this->organizationCorporateName;
    }

    public function getOrganizationForm(): ?string
    {
        return $this->organizationForm;
    }

    public function getOrganizationLogo(): ?SplFileInfo
    {
        return $this->organizationLogo;
    }
}
