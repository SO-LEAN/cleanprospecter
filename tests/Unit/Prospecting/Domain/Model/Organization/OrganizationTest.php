<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Domain\Model\Organization;

use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Domain\Exception\ValidationException;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRegistered;
use Solean\Prospecting\Domain\Model\Shared\Address;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;

final class OrganizationTest extends TestCase
{
    public function testRegisterCreatesOrganizationAndRaisesEvent(): void
    {
        $org = Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
            corporateName: 'ACME',
            email: new Email('contact@acme.com'),
        );

        $this->assertEquals('1', $org->id()->value);
        $this->assertEquals('100', $org->ownerId()->value);
        $this->assertEquals('ACME', $org->corporateName());
        $this->assertEquals('contact@acme.com', $org->email()->value);

        $events = $org->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(OrganizationRegistered::class, $events[0]);
        $this->assertEquals('1', $events[0]->organizationId->value);
    }

    public function testRegisterFailsWithoutCorporateNameOrEmail(): void
    {
        $this->expectException(ValidationException::class);

        Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
        );
    }

    public function testUpdateProfile(): void
    {
        $org = Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
            corporateName: 'ACME',
        );

        $org->updateProfile(
            corporateName: 'New ACME',
            email: new Email('new@acme.com'),
            phoneNumber: new PhoneNumber('0123456789'),
            language: 'FR',
            form: 'SARL',
            type: 'Direct',
            observations: 'Updated',
            address: new Address('10 rue Test', '75001', 'Paris', 'FR'),
            holdingId: null,
        );

        $this->assertEquals('New ACME', $org->corporateName());
        $this->assertEquals('new@acme.com', $org->email()->value);
        $this->assertNotNull($org->address());
    }

    public function testAttachAndRemoveLogo(): void
    {
        $org = Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
            corporateName: 'ACME',
        );

        $logo = new Logo('http://example.com/logo.png', 'png', 1024);
        $org->attachLogo($logo);
        $this->assertNotNull($org->logo());
        $this->assertEquals('http://example.com/logo.png', $org->logo()->url);

        $org->removeLogo();
        $this->assertNull($org->logo());
    }

    public function testPinpoint(): void
    {
        $org = Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
            corporateName: 'ACME',
        );

        $org->pinpoint(new GeoPoint(2.3522, 48.8566));
        $this->assertNotNull($org->geoPoint());
        $this->assertEquals(2.3522, $org->geoPoint()->longitude);
    }

    public function testFullName(): void
    {
        $org = Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
            corporateName: 'ACME',
            form: 'Ltd',
        );

        $this->assertEquals('ACME Ltd', $org->fullName());
    }
}
