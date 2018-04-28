<?php
namespace Tests\Unit\Solean\CleanProspecter\Factory;

use Solean\CleanProspecter\Entity\Address;
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
        $organization->setLanguage('EN');
        $organization->setPhoneNumber('03777666888');
        $organization->setEmail('org@organization.com');
        $organization->setCorporateName('Organization');
        $organization->setForm('Limited Company');
        $organization->setAddress(Address::fromValues('10 Downing Street', 'SW1A 2AA', 'London', 'EN'));
        $organization->setObservations('observ.');
        $organization->setOwnedBy(self::creator());

        return $organization;
    }
    /**
     * Default test organization not persisted
     */
    public static function notPersistedRegular(): Organization
    {
        $organization = OrganizationFactory::regular();
        $organization->setId(null);

        return $organization;
    }
    /**
     * Default test organization without address
     */
    public static function withoutAddress(): Organization
    {
        $organization = OrganizationFactory::regular();
        $organization->setAddress(null);

        return $organization;
    }
    /**
     * holding
     */
    public static function holding() : Organization
    {
        $holding = new Organization();

        $holding->setId(456);
        $holding->setLanguage('LU');
        $holding->setCorporateName('Organization holding');
        $holding->setPhoneNumber('03333333333');
        $holding->setEmail('org@organization-holding.com');
        $holding->setForm('GMBH');
        $holding->setObservations('observ.');

        return $holding;
    }

    /**
     * hold
     */
    public static function hold(): Organization
    {
        $organization = OrganizationFactory::regular();
        $holding = OrganizationFactory::holding();

        $organization->setHoldBy($holding);

        return $organization;
    }

    /**
     * hold not persisted
     */
    public static function notPersistedHold(): Organization
    {
        $organization = OrganizationFactory::hold();
        $organization->setId(null);

        return $organization;
    }

    /**
     * Default test organization without address and not persisted
     */
    public static function notPersistedWithoutAddress(): Organization
    {
        $organization = OrganizationFactory::withoutAddress();
        $organization->setId(null);

        return $organization;
    }

    /**
     * Creator
     */
    public static function creator() : Organization
    {
        $organization = new Organization();

        $organization->setId(777);
        $organization->setLanguage('FR');
        $organization->setPhoneNumber('0999999999');
        $organization->setEmail('org@organization.com');
        $organization->setCorporateName('Prospector Organization');
        $organization->setForm('Limited Company');
        $organization->setObservations('observ.');

        return $organization;
    }
}
