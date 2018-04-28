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
        return new CreateOrganizationRequest(
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
        return new CreateOrganizationRequest(
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
     * Missing corporate name and email
     */
    public static function missingMandatory()
    {
        return new CreateOrganizationRequest(
            777,
            '03333333333',
            null,
            'EN',
            null,
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
     * Missing corporate name and email
     */
    public static function withoutAddress()
    {
        return new CreateOrganizationRequest(
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
