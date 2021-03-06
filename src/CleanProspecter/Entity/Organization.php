<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

use Solean\CleanProspecter\Exception\Entity\ValidationException;

class Organization extends Person implements GeoLocatable
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
     * @var string
     */
    private $type;
    /**
     * @var Address
     */
    private $address;
    /**
     * @var bool
     */
    private $hasAddress;
    /**
     * @var GeoPoint
     */
    private $geoPoint;
    /**
     * @var bool
     */
    private $hasGeoPoint;
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
    /**
     * @var array
     */
    private $stats;

    public function __construct()
    {
        $this->hasAddress = false;
        $this->hasGeoPoint = false;
        $this->hasLogo = false;
        $this->subsidiaries = [];
        $this->applicationUsers = [];
        $this->ownedProspectedOrganizations = [];
        $this->ownedProspects = [];
        $this->ownedCustomers = [];
        $this->stats = [
            'activeOrganizations' => 0,
        ];
    }

    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    public function setCorporateName(?string $corporateName): void
    {
        $this->corporateName = $corporateName;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function setForm(?string $form): void
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

    public function getGeoPoint(): ?GeoPoint
    {
        return $this->hasGeoPoint ? $this->geoPoint : null;
    }

    public function setGeoPoint(?GeoPoint $geoPoint): void
    {
        $this->geoPoint = $geoPoint;
        $this->hasGeoPoint = $geoPoint ? true : false;
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
        $ownedBy->incrementStat('activeOrganizations');
    }

    public function getLogo(): ?File
    {
        return $this->hasLogo ? $this->logo : null;
    }

    public function setLogo(?File $logo): void
    {
        $this->logo = $logo;
        $this->hasLogo = $logo ? true : false;
    }

    public function removeLogo(): void
    {
        $this->setLogo(null);
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

    public function setApplicationUsers(array $applicationUsers): void
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

    public function setStats(array $stats): void
    {
        $this->stats = $stats;
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function getStat($name)
    {
        return $this->stats[$name] ?? 0;
    }

    public function incrementStat($name): void
    {
        $this->stats[$name]++;
    }

    public function validate()
    {
        if (!$this->getOwnedBy()) {
            return;
        }

        if (!$this->getCorporateName() && !$this->getEmail()) {
            throw new ValidationException('At least one is mandatory : corporate name or email', 412, null, '*');
        }
    }
}
