<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\Login;

final class LoginRequest
{
    /**
     * @var string
     */
    private $login;
    /**
     * @var string
     */
    private $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
