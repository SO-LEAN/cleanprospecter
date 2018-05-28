<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateAccountInformation;

use SplFileInfo;

final class UpdateAccountInformationResponse
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
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $pictureUrl;
    /**
     * @var string
     */
    private $pictureExtension;
    /**
     * @var int
     */
    private $pictureSize;
    /**
     * @var string
     */
    private $organizationCorporateName;
    /**
     * @var string
     */
    private $organizationForm;
    /**
     * @var string
     */
    private $organizationLogoUrl;
    /**
     * @var string
     */
    private $organizationLogoExtension;
    /**
     * @var int
     */
    private $organizationLogoSize;

    public function __construct(
        string $userName,
        ?string $firstName,
        ?string $lastName,
        ?string $pictureUrl,
        ?string $pictureExtension,
        ?int $pictureSize,
        ?string $phoneNumber,
        ?string $email,
        ?string $language,
        ?string $organizationCorporateName,
        ?string $organizationForm,
        ?string $organizationLogoUrl,
        ?string $organizationLogoExtension,
        ?int $organizationLogoSize
    ) {
        $this->userName = $userName;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->language = $language;
        $this->organizationCorporateName = $organizationCorporateName;
        $this->organizationForm = $organizationForm;
        $this->organizationLogoUrl = $organizationLogoUrl;
        $this->organizationLogoExtension = $organizationLogoExtension;
        $this->organizationLogoSize = $organizationLogoSize;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    public function getPictureExtension(): ?string
    {
        return $this->pictureExtension;
    }

    public function getPictureSize(): ?int
    {
        return $this->pictureSize;
    }

    public function getOrganizationCorporateName(): string
    {
        return $this->organizationCorporateName;
    }

    public function getOrganizationForm(): ?string
    {
        return $this->organizationForm;
    }

    public function getOrganizationLogoUrl(): ?string
    {
        return $this->organizationLogoUrl;
    }

    public function getOrganizationLogoExtension(): ?string
    {
        return $this->organizationLogoExtension;
    }

    public function getOrganizationLogoSize(): ?int
    {
        return $this->organizationLogoSize;
    }
}
