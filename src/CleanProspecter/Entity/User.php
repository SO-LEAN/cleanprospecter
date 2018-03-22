<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

class User extends Base
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

    public function __construct()
    {
        $this->roles = [];
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

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getUserName() : string
    {
        return $this->userName;
    }

    public function setUserName($userName): void
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
}