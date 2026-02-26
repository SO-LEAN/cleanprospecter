<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowOrganization;

use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;

final class ShowOrganizationHandler
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
    ) {}

    public function __invoke(ShowOrganizationQuery $query, ShowOrganizationPresenter $presenter): void
    {
        $organization = $this->organizationRepository->ofId(new OrganizationId($query->organizationId));

        $readModel = new OrganizationReadModel(
            id: $organization->id()->value,
            ownerId: $organization->ownerId()?->value,
            corporateName: $organization->corporateName(),
            email: $organization->email()?->value,
            phoneNumber: $organization->phoneNumber()?->value,
            language: $organization->language(),
            form: $organization->form(),
            type: $organization->type(),
            observations: $organization->observations(),
            street: $organization->address()?->street,
            postalCode: $organization->address()?->postalCode,
            city: $organization->address()?->city,
            country: $organization->address()?->country,
            longitude: $organization->geoPoint()?->longitude,
            latitude: $organization->geoPoint()?->latitude,
            logoUrl: $organization->logo()?->url,
            logoExtension: $organization->logo()?->extension,
            logoSize: $organization->logo()?->size,
            holdingId: $organization->holdingId()?->value,
        );

        $presenter->present($readModel);
    }
}
