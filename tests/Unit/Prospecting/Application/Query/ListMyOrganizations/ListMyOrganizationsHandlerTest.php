<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Query\ListMyOrganizations;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Application\Query\ListMyOrganizations\ListMyOrganizationsHandler;
use Solean\Prospecting\Application\Query\ListMyOrganizations\ListMyOrganizationsQuery;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\Address;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryOrganizationRepository;
use Tests\Unit\Prospecting\Double\CollectingListMyOrganizationsPresenter;

final class ListMyOrganizationsHandlerTest extends TestCase
{
    private InMemoryOrganizationRepository $organizationRepository;
    private CollectingListMyOrganizationsPresenter $presenter;
    private ListMyOrganizationsHandler $handler;

    protected function setUp(): void
    {
        $this->organizationRepository = new InMemoryOrganizationRepository();
        $this->presenter = new CollectingListMyOrganizationsPresenter();

        $this->handler = new ListMyOrganizationsHandler(
            $this->organizationRepository,
        );

        $owner = Organization::register(
            id: OrganizationId::fromString('100'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Owner Corp',
        );
        $this->organizationRepository->save($owner);
    }

    #[Test]
    public function shouldReturnEmptyListWhenNoOrganizationsOwned(): void
    {
        ($this->handler)(new ListMyOrganizationsQuery(ownerOrganizationId: '999'), $this->presenter);

        $this->assertEquals(1, $this->presenter->currentPage);
        $this->assertEquals(0, $this->presenter->total);
        $this->assertEquals(0, $this->presenter->totalPages);
        $this->assertCount(0, $this->presenter->organizations);
    }

    #[Test]
    public function shouldListOrganizationsWithReadModelMapping(): void
    {
        $org1 = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
            form: 'SARL',
            address: Address::create('10 rue Test', '75001', 'Paris', 'FR'),
            logo: Logo::create('http://storage.test/logo1.png', 'png', 1000),
        );
        $org1->pinpoint(GeoPoint::fromCoordinates(2.3522, 48.8566));
        $this->organizationRepository->save($org1);

        $org2 = Organization::register(
            id: OrganizationId::fromString('2'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Beta Corp',
        );
        $this->organizationRepository->save($org2);

        ($this->handler)(new ListMyOrganizationsQuery(ownerOrganizationId: '100'), $this->presenter);

        $this->assertEquals(1, $this->presenter->currentPage);
        $this->assertEquals(3, $this->presenter->total); // owner + 2 organizations
        $this->assertEquals(1, $this->presenter->totalPages);
        $this->assertCount(3, $this->presenter->organizations);

        $acme = null;
        foreach ($this->presenter->organizations as $org) {
            if ($org->id === '1') {
                $acme = $org;
            }
        }
        $this->assertNotNull($acme);
        $this->assertEquals('ACME SARL', $acme->fullName);
        $this->assertEquals('Paris', $acme->city);
        $this->assertEquals('FR', $acme->country);
        $this->assertEquals('75001', $acme->postalCode);
        $this->assertEquals('http://storage.test/logo1.png', $acme->logoUrl);
        $this->assertEquals(2.3522, $acme->longitude);
        $this->assertEquals(48.8566, $acme->latitude);
    }

    #[Test]
    public function shouldFilterBySearchQuery(): void
    {
        $org1 = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
        );
        $this->organizationRepository->save($org1);

        $org2 = Organization::register(
            id: OrganizationId::fromString('2'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Beta Corp',
        );
        $this->organizationRepository->save($org2);

        ($this->handler)(new ListMyOrganizationsQuery(ownerOrganizationId: '100', query: 'ACME'), $this->presenter);

        $this->assertEquals(1, $this->presenter->total);
        $this->assertCount(1, $this->presenter->organizations);
        $this->assertEquals('1', $this->presenter->organizations[0]->id);
    }

    #[Test]
    public function shouldPaginateResults(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $org = Organization::register(
                id: OrganizationId::fromString((string) (200 + $i)),
                ownerId: OrganizationId::fromString('100'),
                corporateName: "Org $i",
            );
            $this->organizationRepository->save($org);
        }

        ($this->handler)(new ListMyOrganizationsQuery(ownerOrganizationId: '100', page: 1, maxPerPage: 2), $this->presenter);

        $this->assertEquals(1, $this->presenter->currentPage);
        $this->assertEquals(6, $this->presenter->total);
        $this->assertEquals(3, $this->presenter->totalPages);
        $this->assertCount(2, $this->presenter->organizations);

        ($this->handler)(new ListMyOrganizationsQuery(ownerOrganizationId: '100', page: 3, maxPerPage: 2), $this->presenter);

        $this->assertEquals(3, $this->presenter->currentPage);
        $this->assertCount(2, $this->presenter->organizations);
    }

    #[Test]
    public function shouldOnlyShowOwnedOrganizations(): void
    {
        $otherOwner = Organization::register(
            id: OrganizationId::fromString('200'),
            ownerId: OrganizationId::fromString('200'),
            corporateName: 'Other Owner',
        );
        $this->organizationRepository->save($otherOwner);

        $otherOrg = Organization::register(
            id: OrganizationId::fromString('3'),
            ownerId: OrganizationId::fromString('200'),
            corporateName: 'Other Org',
        );
        $this->organizationRepository->save($otherOrg);

        $myOrg = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'My Org',
        );
        $this->organizationRepository->save($myOrg);

        ($this->handler)(new ListMyOrganizationsQuery(ownerOrganizationId: '100'), $this->presenter);

        $this->assertEquals(2, $this->presenter->total);
        $ids = array_map(fn ($o) => $o->id, $this->presenter->organizations);
        $this->assertContains('100', $ids);
        $this->assertContains('1', $ids);
        $this->assertNotContains('200', $ids);
        $this->assertNotContains('3', $ids);
    }
}
