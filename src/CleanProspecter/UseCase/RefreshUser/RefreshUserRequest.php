<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\RefreshUser;

final class RefreshUserRequest
{
    /**
     * @var string
     */
    private $login;

    public function __construct(string $login)
    {
        $this->login = $login;
    }

    public function getLogin(): string
    {
        return $this->login;
    }
}
