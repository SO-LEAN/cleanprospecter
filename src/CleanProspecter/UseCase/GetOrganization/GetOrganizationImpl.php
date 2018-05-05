<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetOrganization;

use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\Exception\UseCase\NotFoundException;

final class GetOrganizationImpl extends AbstractUseCase implements GetOrganization
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

    public function execute(GetOrganizationRequest $request, GetOrganizationPresenter $presenter)
    {
        $persisted = $this->getOrganization($request);

        return $presenter->present($this->buildResponse($persisted));
    }

    private function getOrganization(GetOrganizationRequest $request): Organization
    {
        try {
            $persisted = $this->organizationGateway->get($request->getId());
        } catch (Gateway\NotFoundException $e) {
            throw new NotFoundException(sprintf('Organization with #ID %d not found', $request->getId()), 404, $e, ['id' => 'Organization not found']);
        }

        return $persisted;
    }

    private function buildResponse(Organization $persisted): GetOrganizationResponse
    {
        return new GetOrganizationResponse(
            $persisted->getId(),
            $persisted->getOwnedBy() ? $persisted->getOwnedBy()->getId() : null,
            $persisted->getPhoneNumber(),
            $persisted->getEmail(),
            $persisted->getLanguage(),
            $persisted->getCorporateName(),
            $persisted->getForm(),
            $persisted->getAddress() ? $persisted->getAddress()->getStreet() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getPostalCode() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getCity() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getCountry() : null,
            $persisted->getGeoPoint() ? $persisted->getGeoPoint()->getLongitude() : null,
            $persisted->getGeoPoint() ? $persisted->getGeoPoint()->getLatitude() : null,
            $persisted->getObservations(),
            $persisted->getLogo() ? $persisted->getLogo()->getUrl() : null,
            $persisted->getLogo() ? $persisted->getLogo()->getExtension() : null,
            $persisted->getLogo() ? $persisted->getLogo()->getSize() : null,
            $persisted->getHoldBy() ? $persisted->getHoldBy()->getId() : null
        );
    }
}
