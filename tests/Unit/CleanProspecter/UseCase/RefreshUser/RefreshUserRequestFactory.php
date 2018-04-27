<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RefreshUser;

use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserRequest;

class RefreshUserRequestFactory
{
    /**
     * User with credential login/password
     */
    public static function regular()
    {
        return new RefreshUserRequest('login');
    }
}
