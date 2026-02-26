<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ListMyOrganizations;

use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;

final class ListMyOrganizationsHandler
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
    ) {}

    public function __invoke(ListMyOrganizationsQuery $query, ListMyOrganizationsPresenter $presenter): void
    {
        $result = $this->organizationRepository->findPageByOwner(
            ownerId: OrganizationId::fromString($query->ownerOrganizationId),
            page: $query->page,
            maxPerPage: $query->maxPerPage,
            query: $query->query,
        );

        $readModels = array_map(
            static fn ($org) => new OrganizationSummaryReadModel(
                id: $org->id()->value,
                fullName: $org->fullName(),
                city: $org->address()?->city,
                country: $org->address()?->country,
                postalCode: $org->address()?->postalCode,
                logoUrl: $org->logo()?->url,
                longitude: $org->geoPoint()?->longitude,
                latitude: $org->geoPoint()?->latitude,
            ),
            $result['organizations'],
        );

        $presenter->present(
            currentPage: $result['currentPage'],
            total: $result['total'],
            totalPages: $result['totalPages'],
            organizations: $readModels,
        );
    }
}
