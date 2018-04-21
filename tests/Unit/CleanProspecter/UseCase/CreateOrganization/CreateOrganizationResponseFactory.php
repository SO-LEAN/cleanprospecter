<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationResponse;

class CreateOrganizationResponseFactory
{
    /**
     * Default test organization
     */
    public static function regular()
    {
        return new CreateOrganizationResponse(123);
    }
    /**
     * Default test organization
     */
    public static function hold()
    {
        return CreateOrganizationResponseFactory::regular();
    }
}
