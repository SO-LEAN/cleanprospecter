<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

class Customer extends Person
{
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var User
     */
    private $createdBy;
    /**
     * @var Organization
     */
    private $ownedBy;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getOwnedBy(): Organization
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(Organization $ownedBy): void
    {
        $this->ownedBy = $ownedBy;
    }
}
