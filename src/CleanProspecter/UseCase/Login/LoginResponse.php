<?php
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
    private $password;
    /**
     * @var string
     */
    private $userName;

    public function __construct(array $roles, string $password, string $userName)
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