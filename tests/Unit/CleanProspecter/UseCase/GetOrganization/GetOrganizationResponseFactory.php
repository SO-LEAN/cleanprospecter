<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetOrganization;

use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationResponse;

class GetOrganizationResponseFactory
{
    /**
     * Default test organization
     */
    public static function regular()
    {
        return new GetOrganizationResponse(
            123,
            777,
            '03777666888',
            'org@organization.com',
            'EN',
            'Organization',
            'Limited Company',
            '10 Downing Street',
            'SW1A 2AA',
            'London',
            'EN',
            'observ.',
            null
        );
    }
    /**
     * Default test organization
     */
    public static function hold()
    {
        return new GetOrganizationResponse(
            123,
            777,
            '03777666888',
            'org@organization.com',
            'EN',
            'Organization',
            'Limited Company',
            '10 Downing Street',
            'SW1A 2AA',
            'London',
            'EN',
            'observ.',
            456
        );
    }
    /**
     * Default test organization
     */
    public static function withoutAddress()
    {
        return new GetOrganizationResponse(
            123,
            777,
            '03777666888',
            'org@organization.com',
            'EN',
            'Organization',
            'Limited Company',
            null,
            null,
            null,
            null,
            'observ.',
            null
        );
    }
}
