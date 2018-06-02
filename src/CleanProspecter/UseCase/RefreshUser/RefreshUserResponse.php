<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\RefreshUser;

final class RefreshUserResponse
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
     * @var string
     */
    private $pictureUrl;
    /**
     * @var mixed
     */
    private $organizationId;

    public function __construct($id, array $roles, string $userName, string $password, ?string $pictureUrl, $organizationId)
    {
        $this->id = $id;
        $this->roles = $roles;
        $this->password = $password;
        $this->userName = $userName;
        $this->pictureUrl = $pictureUrl;
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

    public function getPictureUrl(): ?string
    {
        return $this->userName;
    }

    public function getOrganizationId()
    {
        return $this->organizationId;
    }
}
