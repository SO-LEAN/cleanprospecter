<?php
namespace Tests\Unit\Solean\CleanProspecter\Factory;

use Solean\CleanProspecter\Entity\Organization;

class OrganizationFactory
{
    /**
     * Default test organization
     */
    public static function regular() : Organization
    {
        $organization = new Organization();

        $organization->setId(123);
        $organization->setCountry('DE');
        $organization->setEmail('org@organization.com');
        $organization->setCorporateName('Organization');
        $organization->setForm('GMBH');

        return $organization;
    }
    /**
     * Default test organization not persisted
     */
    public static function notPersistedRegular() : Organization
    {
        $organization = OrganizationFactory::regular();
        $organization->setId(null);

        return $organization;
    }

    /**
     * holding
     */
    public static function holding() : Organization
    {
        $holding = new Organization();

        $holding->setId(456);
        $holding->setCountry('LU');
        $holding->setCorporateName('Organization holding');
        $holding->setEmail('org@organization-holding.com');
        $holding->setForm('GMBH');

        return $holding;
    }

    /**
     * hold
     */
    public static function hold() : Organization
    {
        $organization = OrganizationFactory::regular();
        $holding = OrganizationFactory::holding();

        $organization->setHoldBy($holding);

        return $organization;
    }

    /**
     * hold not persisted
     */
    public static function notPersistedHold() : Organization
    {
        $organization = OrganizationFactory::hold();
        $organization->setId(null);

        return $organization;
    }
}
