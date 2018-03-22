<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindByUserName;

use Solean\CleanProspecter\UseCase\FindByUserName\FindByUserNameRequest;

class FindByUserNameRequestFactory
{
    /**
     * User with credential login/password
     */
    public static function regular()
    {
        return new FindByUserNameRequest('login', 'password');
    }
}