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
        return new CreateOrganizationResponse(
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
            null,
            null,
            null,
            null
        );
    }
    /**
     * Default test organization
     */
    public static function hold()
    {
        return new CreateOrganizationResponse(
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
            null,
            null,
            null,
            456
        );
    }
    /**
     * Default test organization
     */
    public static function withoutAddress()
    {
        return new CreateOrganizationResponse(
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
            null,
            null,
            null,
            null
        );
    }
}
