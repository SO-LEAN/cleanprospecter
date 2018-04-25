<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetOrganization;



use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\UseCase\Presenter;
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

    public function execute(GetOrganizationRequest $request, Presenter $presenter)
    {
        $persisted = $this->getOrganization($request);

        return $presenter->present($this->buildResponse($persisted));
    }

    private function getOrganization(GetOrganizationRequest $request): Organization
    {
        try {
            $persisted = $this->organizationGateway->get($request->getId());
        } catch (Gateway\NotFoundException $e) {
            throw new NotFoundException(sprintf('Holding with #ID %d not found', $request->getId()), 404, $e, ['holdBy' => 'Holding not found']);
        }

        return $persisted;
    }

    private function buildResponse(Organization $persisted): GetOrganizationResponse
    {
        return new GetOrganizationResponse(
            $persisted->getId(),
            $persisted->getOwnedBy()->getId(),
            $persisted->getEmail(),
            $persisted->getLanguage(),
            $persisted->getCorporateName(),
            $persisted->getForm(),
            $persisted->getAddress() ? $persisted->getAddress()->getStreet() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getPostalCode() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getCity() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getCountry() : null,
            $persisted->getHoldBy() ? $persisted->getHoldBy()->getId() : null
        );
    }

}
