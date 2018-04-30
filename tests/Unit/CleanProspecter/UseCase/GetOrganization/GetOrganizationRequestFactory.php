<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetOrganization;

use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationRequest;

class GetOrganizationRequestFactory
{
    /**
     * Default test organization
     */
    public static function regular()
    {
        return new GetOrganizationRequest(777);
    }
    /**
     * Default test organization
     */
    public static function hold()
    {
        return new GetOrganizationRequest(777);
    }
    /**
     * Missing corporate name and email
     */
    public static function missingMandatory()
    {
        return new GetOrganizationRequest(777);
    }
    /**
     * Missing corporate name and email
     */
    public static function withoutAddress()
    {
        return new GetOrganizationRequest(777);
    }
}
