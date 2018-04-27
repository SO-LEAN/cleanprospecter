<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindByUserName;

use Tests\Unit\Solean\CleanProspecter\Factory\UserFactory;
use Solean\CleanProspecter\UseCase\FindByUserName\FindByUserNameResponse;

class FindByUserNameResponseFactory
{
    /**
     * User with credential login/password/salt/ROLE
     */
    public static function regular()
    {
        return new FindByUserNameResponse(123, ['ROLE'], 'login', UserFactory::regular()->getPassword(), 777);
    }
}
