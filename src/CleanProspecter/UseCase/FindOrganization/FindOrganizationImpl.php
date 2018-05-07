<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindOrganization;

use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationResponse as Dto;

final class FindOrganizationImpl extends AbstractUseCase implements FindOrganization
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

    public function execute(FindOrganizationRequest $request, FindOrganizationPresenter $presenter)
    {
        $page = $this->organizationGateway->findPageByQuery($request->getPage(), $request->getQuery());

        $response = new FindOrganizationResponse($page->getNumber(), $page->getTotal(), $this->organizationToDto($page->getContent()));

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
                $organization->getLogo() ? $organization->getLogo()->getUrl() : null
            );
        }

        return $dtoList;
    }
}
