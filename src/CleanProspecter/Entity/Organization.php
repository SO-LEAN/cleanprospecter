<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

class Organization extends Person
{
    /**
     * @var string
     */
    private $corporateName;
    /**
     * @var string
     */
    private $form;
    /**
     * @var Address
     */
    private $address;
    /**
     * @var bool
     */
    private $hasAddress;
    /**
     * @var Organization
     */
    private $holdBy;
    /**
     * @var Organization
     */
    private $ownedBy;
    /**
     * @var File
     */
    private $logo;
    /**
     * @var bool
     */
    private $hasLogo;
    /**
     * @var Organization[]
     */
    private $subsidiaries;
    /**
     * @var User[]
     */
    private $applicationUsers;
    /**
     * @var Organization[]
     */
    private $ownedProspectedOrganizations;
    /**
     * @var Prospect[]
     */
    private $ownedProspects;
    /**
     * @var Customer[]
     */
    private $ownedCustomers;

    public function __construct()
    {
        $this->hasAddress = false;
        $this->hasLogo = false;
        $this->subsidiaries = [];
        $this->applicationUsers = [];
        $this->ownedProspectedOrganizations = [];
        $this->ownedProspects = [];
        $this->ownedCustomers = [];
    }

    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    public function setCorporateName(string $corporateName): void
    {
        $this->corporateName = $corporateName;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function setForm(string $form): void
    {
        $this->form = $form;
    }

    public function getAddress(): ?Address
    {
        return $this->hasAddress ? $this->address : null;
    }

    public function setAddress(?Address $address): void
    {
        $this->address = $address;
        $this->hasAddress = $address ? true : false;
    }

    public function getHoldBy(): ?Organization
    {
        return $this->holdBy;
    }

    public function setHoldBy(?Organization $holdBy): void
    {
        $this->holdBy = $holdBy;
    }

    public function getOwnedBy(): ?Organization
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(Organization $ownedBy): void
    {
        $this->ownedBy = $ownedBy;
    }

    public function getLogo(): ?File
    {
        return $this->hasLogo ? $this->logo : null;
    }

    public function setLogo(File $logo): void
    {
        $this->logo = $logo;
        $this->hasLogo = $logo ? true : false;
    }

    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->corporateName, $this->form));
    }

    public function getSubsidiaries(): array
    {
        return $this->subsidiaries;
    }

    public function setSubsidiaries(array $subsidiaries): void
    {
        $this->subsidiaries = $subsidiaries;
    }

    public function addSubsidiary(Organization $subsidiary): void
    {
        $this->subsidiaries[] = $subsidiary;
        $subsidiary->setHoldBy($this);
    }

    public function getApplicationUsers(): array
    {
        return $this->applicationUsers;
    }

    public function setApplicationUsers(User $applicationUsers): void
    {
        $this->applicationUsers = $applicationUsers;
    }

    public function addApplicationUsers(User $user): void
    {
        $this->applicationUsers[] = $user;
        $user->setOrganization($this);
    }

    public function getOwnedProspectedOrganizations(): array
    {
        return $this->ownedProspectedOrganizations;
    }

    public function setOwnedProspectedOrganizations(array $ownedProspectedOrganizations): void
    {
        $this->ownedProspectedOrganizations = $ownedProspectedOrganizations;
    }

    public function addOwnedProspectedOrganizations(Organization $ownedOrganization): void
    {
        $this->ownedProspectedOrganizations[] = $ownedOrganization;
        $ownedOrganization->setOwnedBy($this);
    }

    public function getOwnedProspects(): array
    {
        return $this->ownedProspects;
    }

    public function setOwnedProspects(array $ownedProspects): void
    {
        $this->ownedProspects = $ownedProspects;
    }

    public function addOwnedProspects(Prospect $ownedProspect): void
    {
        $this->ownedProspects[] = $ownedProspect;
        $ownedProspect->setOwnedBy($this);
    }

    public function getOwnedCustomers(): array
    {
        return $this->ownedCustomers;
    }


    public function setOwnedCustomers(array $ownedCustomers): void
    {
        $this->ownedCustomers = $ownedCustomers;
    }

    public function addOwnedCustomers(Customer $ownedCustomer): void
    {
        $this->ownedProspects[] = $ownedCustomer;
        $ownedCustomer->setOwnedBy($this);
    }
}
