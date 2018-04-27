<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindByUserName;

final class FindByUserNameResponse
{
    /**
     * @var mixed
     */
    private $id;
    /**
     * @var array
     */
    private $roles;
    /**
     * @var string
     */
    private $userName;
    /**
     * @var string
     */
    private $password;
    /**
     * @var mixed
     */
    private $organizationId;

    public function __construct($id, array $roles, string $userName, string $password, $organizationId)
    {
        $this->id = $id;
        $this->roles = $roles;
        $this->password = $password;
        $this->userName = $userName;
        $this->organizationId = $organizationId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getOrganizationId()
    {
        return $this->organizationId;
    }
}
