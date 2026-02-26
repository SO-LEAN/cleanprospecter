<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Command\UpdateOrganizationProfile;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Application\Command\UpdateOrganizationProfile\UpdateOrganizationProfileCommand;
use Solean\Prospecting\Application\Command\UpdateOrganizationProfile\UpdateOrganizationProfileHandler;
use Solean\Prospecting\Domain\Exception\OrganizationNotFoundException;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryOrganizationRepository;
use Tests\Unit\Prospecting\Double\FakeFileStorage;
use Tests\Unit\Prospecting\Double\SpyNotifier;
use Tests\Unit\Prospecting\Double\StubGeoLocationService;

final class UpdateOrganizationProfileHandlerTest extends TestCase
{
    private InMemoryOrganizationRepository $organizationRepository;
    private FakeFileStorage $fileStorage;
    private StubGeoLocationService $geoLocation;
    private SpyNotifier $notifier;
    private UpdateOrganizationProfileHandler $handler;

    protected function setUp(): void
    {
        $this->organizationRepository = new InMemoryOrganizationRepository();
        $this->fileStorage = new FakeFileStorage();
        $this->geoLocation = new StubGeoLocationService();
        $this->notifier = new SpyNotifier();

        $this->handler = new UpdateOrganizationProfileHandler(
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

        $org = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
            language: 'EN',
        );
        $this->organizationRepository->save($org);
    }

    #[Test]
    public function shouldUpdateProfile(): void
    {
        $command = new UpdateOrganizationProfileCommand(
            organizationId: '1',
            corporateName: 'New ACME',
            email: 'new@acme.com',
            language: 'FR',
            form: 'SARL',
            type: 'Direct',
        );

        ($this->handler)($command);

        $updated = $this->organizationRepository->ofId(OrganizationId::fromString('1'));
        $this->assertEquals('New ACME', $updated->corporateName());
        $this->assertEquals('new@acme.com', $updated->email()->value);
        $this->assertCount(1, $this->notifier->successes);
    }

    #[Test]
    public function shouldUpdateWithAddress(): void
    {
        $this->geoLocation->willReturn(GeoPoint::fromCoordinates(7.7663456, 48.5554971));

        $command = new UpdateOrganizationProfileCommand(
            organizationId: '1',
            corporateName: 'ACME',
            street: '20 avenue du Neuhof',
            postalCode: '67100',
            city: 'Strasbourg',
            country: 'FR',
        );

        ($this->handler)($command);

        $updated = $this->organizationRepository->ofId(OrganizationId::fromString('1'));
        $this->assertNotNull($updated->address());
        $this->assertNotNull($updated->geoPoint());
        $this->assertEquals(7.7663456, $updated->geoPoint()->longitude);
    }

    #[Test]
    public function shouldUpdateWithHolding(): void
    {
        $holding = Organization::register(
            id: OrganizationId::fromString('200'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Holding Corp',
        );
        $this->organizationRepository->save($holding);

        $command = new UpdateOrganizationProfileCommand(
            organizationId: '1',
            corporateName: 'ACME',
            holdingId: '200',
        );

        ($this->handler)($command);

        $updated = $this->organizationRepository->ofId(OrganizationId::fromString('1'));
        $this->assertNotNull($updated->holdingId());
        $this->assertEquals('200', $updated->holdingId()->value);
    }

    #[Test]
    public function shouldThrowWhenOrganizationNotFound(): void
    {
        $command = new UpdateOrganizationProfileCommand(
            organizationId: '999',
            corporateName: 'ACME',
        );

        $this->expectException(OrganizationNotFoundException::class);
        ($this->handler)($command);
    }

    #[Test]
    public function shouldThrowWhenHoldingNotFound(): void
    {
        $command = new UpdateOrganizationProfileCommand(
            organizationId: '1',
            corporateName: 'ACME',
            holdingId: '999',
        );

        $this->expectException(OrganizationNotFoundException::class);
        ($this->handler)($command);
    }
}
