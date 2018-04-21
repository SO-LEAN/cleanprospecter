<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationRequest;

class CreateOrganizationRequestFactory
{
    /**
     * Default test organization
     */
    public static function regular()
    {
        return new CreateOrganizationRequest('org@organization.com', 'DE', 'Organization', 'GMBH', null);
    }
    /**
     * Default test organization
     */
    public static function hold()
    {
        return new CreateOrganizationRequest('org@organization.com', 'DE', 'Organization', 'GMBH', 456);
    }
}
