<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

class User extends Person
{
    /**
     * @var array
     */
    private $roles;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $userName;
    /**
     * @var string
     */
    private $salt;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var File
     */
    private $picture;
    /**
     * @var bool
     */
    private $hasPicture;
    /**
     * @var Organization
     */
    private $organization;

    public function __construct()
    {
        $this->roles = [];
        $this->hasPicture = false;
    }

    public function getRoles() : array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(string $role) : void
    {
        $this->roles[] = $role;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getUserName() : string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPicture(): ?File
    {
        return $this->hasPicture ? $this->picture : null;
    }

    public function setPicture(File $picture): void
    {
        $this->picture = $picture;
        $this->hasPicture = $picture ? true : false;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function encodePassword(): string
    {
        $this->password = md5(sprintf('%s%s', $this->password, $this->salt));

        return $this->password;
    }
}
