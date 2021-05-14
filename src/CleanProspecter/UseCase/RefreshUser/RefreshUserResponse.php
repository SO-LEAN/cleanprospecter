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

    public function __construct($id, array $roles, string $userName, string $password, $organizationId, ?string $pictureUrl)
    {
        $this->id = $id;
        $this->roles = $roles;
        $this->password = $password;
        $this->userName = $userName;
        $this->organizationId = $organizationId;
        $this->pictureUrl = $pictureUrl;
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
        return $this->pictureUrl;
    }

    public function getOrganizationId()
    {
        return $this->organizationId;
    }
}
