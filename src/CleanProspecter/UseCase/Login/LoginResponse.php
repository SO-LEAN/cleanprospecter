<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\Login;

final class LoginResponse
{
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

    public function __construct(array $roles, string $userName, string $password)
    {
        $this->roles = $roles;
        $this->password = $password;
        $this->userName = $userName;
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
}
