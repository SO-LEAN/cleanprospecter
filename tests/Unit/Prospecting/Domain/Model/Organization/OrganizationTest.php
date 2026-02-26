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
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
            email: Email::fromString('contact@acme.com'),
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
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
        );
    }

    public function testUpdateProfile(): void
    {
        $org = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
        );

        $org->updateProfile(
            corporateName: 'New ACME',
            email: Email::fromString('new@acme.com'),
            phoneNumber: PhoneNumber::fromString('0123456789'),
            language: 'FR',
            form: 'SARL',
            type: 'Direct',
            observations: 'Updated',
            address: Address::create('10 rue Test', '75001', 'Paris', 'FR'),
            holdingId: null,
        );

        $this->assertEquals('New ACME', $org->corporateName());
        $this->assertEquals('new@acme.com', $org->email()->value);
        $this->assertNotNull($org->address());
    }

    public function testAttachAndRemoveLogo(): void
    {
        $org = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
        );

        $logo = Logo::create('http://example.com/logo.png', 'png', 1024);
        $org->attachLogo($logo);
        $this->assertNotNull($org->logo());
        $this->assertEquals('http://example.com/logo.png', $org->logo()->url);

        $org->removeLogo();
        $this->assertNull($org->logo());
    }

    public function testPinpoint(): void
    {
        $org = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
        );

        $org->pinpoint(GeoPoint::fromCoordinates(2.3522, 48.8566));
        $this->assertNotNull($org->geoPoint());
        $this->assertEquals(2.3522, $org->geoPoint()->longitude);
    }

    public function testFullName(): void
    {
        $org = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
            form: 'Ltd',
        );

        $this->assertEquals('ACME Ltd', $org->fullName());
    }
}
