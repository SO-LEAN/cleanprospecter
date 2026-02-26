<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Command\RegisterOrganization;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Application\Command\RegisterOrganization\RegisterOrganizationCommand;
use Solean\Prospecting\Application\Command\RegisterOrganization\RegisterOrganizationHandler;
use Solean\Prospecting\Domain\Exception\OrganizationNotFoundException;
use Solean\Prospecting\Domain\Exception\ValidationException;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryOrganizationRepository;
use Tests\Unit\Prospecting\Double\FakeFileStorage;
use Tests\Unit\Prospecting\Double\SpyNotifier;
use Tests\Unit\Prospecting\Double\StubGeoLocationService;

final class RegisterOrganizationHandlerTest extends TestCase
{
    private InMemoryOrganizationRepository $organizationRepository;
    private FakeFileStorage $fileStorage;
    private StubGeoLocationService $geoLocation;
    private SpyNotifier $notifier;
    private RegisterOrganizationHandler $handler;

    protected function setUp(): void
    {
        $this->organizationRepository = new InMemoryOrganizationRepository();
        $this->fileStorage = new FakeFileStorage();
        $this->geoLocation = new StubGeoLocationService();
        $this->notifier = new SpyNotifier();

        $this->handler = new RegisterOrganizationHandler(
            $this->organizationRepository,
            $this->fileStorage,
            $this->geoLocation,
            $this->notifier,
        );

        $owner = Organization::register(
            id: OrganizationId::fromString('100'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Owner Corp',
        );
        $this->organizationRepository->save($owner);
    }

    #[Test]
    public function shouldRegisterWithMinimalData(): void
    {
        $command = new RegisterOrganizationCommand(
            ownerId: '100',
            corporateName: 'ACME',
        );

        ($this->handler)($command);

        $this->assertEquals(2, $this->organizationRepository->count());
        $this->assertCount(1, $this->notifier->successes);
        $this->assertEquals('Organization created !', $this->notifier->successes[0]);
    }

    #[Test]
    public function shouldRegisterWithAddress(): void
    {
        $this->geoLocation->willReturn(GeoPoint::fromCoordinates(7.7663456, 48.5554971));

        $command = new RegisterOrganizationCommand(
            ownerId: '100',
            corporateName: 'ACME',
            street: '10 Downing Street',
            postalCode: 'SW1A 2AA',
            city: 'London',
            country: 'EN',
        );

        ($this->handler)($command);

        $this->assertEquals(2, $this->organizationRepository->count());
    }

    #[Test]
    public function shouldRegisterWithHolding(): void
    {
        $holding = Organization::register(
            id: OrganizationId::fromString('200'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Holding Corp',
        );
        $this->organizationRepository->save($holding);

        $command = new RegisterOrganizationCommand(
            ownerId: '100',
            corporateName: 'Subsidiary',
            holdingId: '200',
        );

        ($this->handler)($command);

        $this->assertEquals(3, $this->organizationRepository->count());
    }

    #[Test]
    public function shouldThrowWhenOwnerNotFound(): void
    {
        $command = new RegisterOrganizationCommand(
            ownerId: '999',
            corporateName: 'ACME',
        );

        $this->expectException(OrganizationNotFoundException::class);
        ($this->handler)($command);
    }

    #[Test]
    public function shouldThrowWhenHoldingNotFound(): void
    {
        $command = new RegisterOrganizationCommand(
            ownerId: '100',
            corporateName: 'ACME',
            holdingId: '999',
        );

        $this->expectException(OrganizationNotFoundException::class);
        ($this->handler)($command);
    }

    #[Test]
    public function shouldThrowWhenMissingCorporateNameAndEmail(): void
    {
        $command = new RegisterOrganizationCommand(
            ownerId: '100',
        );

        $this->expectException(ValidationException::class);
        ($this->handler)($command);
    }
}
