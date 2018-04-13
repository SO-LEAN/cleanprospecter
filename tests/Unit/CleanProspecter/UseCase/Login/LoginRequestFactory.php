<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;


use Solean\CleanProspecter\UseCase\Login\LoginRequest;

class LoginRequestFactory
{
    /**
     * User with credential login/password
     */
    public static function regular()
    {
        return new LoginRequest('login', 'password');
    }
    /**
     * User with bad password
     */
    public static function typo()
    {
        return new LoginRequest('login', 'passwerd');
    }
    /**
     * Unknown user
     */
    public static function unknown()
    {
        return new LoginRequest('login', 'password');
    }
}
