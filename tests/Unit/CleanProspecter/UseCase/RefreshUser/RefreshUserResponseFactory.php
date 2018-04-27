<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RefreshUser;

use Tests\Unit\Solean\CleanProspecter\Factory\UserFactory;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserResponse;

class RefreshUserResponseFactory
{
    /**
     * User with credential login/password/salt/ROLE
     */
    public static function regular()
    {
        return new RefreshUserResponse(123, ['ROLE'], 'login', UserFactory::regular()->getPassword(), 777);
    }
}
