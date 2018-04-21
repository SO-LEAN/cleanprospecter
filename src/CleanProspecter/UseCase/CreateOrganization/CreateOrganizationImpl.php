<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

use Solean\CleanProspecter\Exception\GateWay;
use Solean\CleanProspecter\Exception\UseCase\NotFoundException;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;

final class CreateOrganizationImpl implements CreateOrganization
{
    /**
     * @var OrganizationGateway
     */
    private $organizationGateway;

    public function __construct(OrganizationGateway $organizationGateway)
    {
        $this->organizationGateway = $organizationGateway;
    }

    public function execute(CreateOrganizationRequest $request, Presenter $presenter): ?object
    {
        $organization = $this->buildOrganization($request);
        $this->appendToHoldingIfNeeded($request, $organization);

        $persisted = $this->organizationGateway->create($organization);

        return $presenter->present($persisted);
    }

    private function buildOrganization(CreateOrganizationRequest $request): Organization
    {
        $organization = new Organization();
        $organization->setCountry($request->getCountry());
        $organization->setEmail($request->getEmail());
        $organization->setCorporateName($request->getCorporateName());
        $organization->setForm($request->getForm());

        return $organization;
    }

    private function appendToHoldingIfNeeded(CreateOrganizationRequest $request, Organization $organization): void
    {
        if ($request->getHoldBy()) {
            try {
                $organization->setHoldBy($this->organizationGateway->get($request->getHoldBy()));
            } catch (GateWay\NotFoundException $e) {
                throw new NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $e, ['holdBy' => 'Holding not found']);
            }
        }
    }
}
