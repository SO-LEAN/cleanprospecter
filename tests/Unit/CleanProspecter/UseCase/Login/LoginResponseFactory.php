<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;

use Solean\CleanProspecter\UseCase\Login\LoginResponse;

class LoginResponseFactory
{
    /**
     * User with credential login/password/salt/ROLE
     */
    public static function regular()
    {
        return new LoginResponse(['ROLE'], 'login', 'password');
    }
}