<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\PageRequest;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsResponse as Dto;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;

final class FindMyOwnOrganizationsImpl extends AbstractUseCase implements FindMyOwnOrganizations
{
    /**
     * @var OrganizationGateway
     */
    private $organizationGateway;

    public function __construct(OrganizationGateway $organizationGateway)
    {
        $this->organizationGateway = $organizationGateway;
    }

    public function canBeExecutedBy(): array
    {
        return ['ROLE_PROSPECTOR'];
    }

    public function execute(FindMyOwnOrganizationsRequest $request, FindMyOwnOrganizationsPresenter $presenter, UseCaseConsumer $consumer)
    {
        $page = $this->organizationGateway->findPageByQuery(new PageRequest($request->getPage(), $request->getQuery(), $request->getMaxByPage(), ['ownedBy' => $consumer->getOrganizationId()]));

        $response = new FindMyOwnOrganizationsResponse($page->getNumber(), $page->getTotal(), $page->getTotalPages(), $this->organizationToDto($page->getContent()));

        return $presenter->present($response);
    }

    /**
     * @param Organization[]
     *
     * @return Dto\Organization[]
     */
    private function organizationToDto(array $organizations): array
    {
        $dtoList = [];

        foreach ($organizations as $organization) {
            $dtoList[] = new Dto\Organization(
                $organization->getId(),
                $organization->getFullName(),
                $organization->getAddress() ? $organization->getAddress()->getCity() : null,
                $organization->getAddress() ? $organization->getAddress()->getCountry() : null,
                $organization->getAddress() ? $organization->getAddress()->getPostalCode() : null,
                $organization->getLogo() ? $organization->getLogo()->getUrl() : null,
                $organization->getGeoPoint() ? $organization->getGeoPoint()->getLongitude() : null,
                $organization->getGeoPoint() ? $organization->getGeoPoint()->getLatitude() : null
            );
        }

        return $dtoList;
    }
}
