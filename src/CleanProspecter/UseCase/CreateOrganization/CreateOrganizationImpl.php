<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

use Solean\CleanProspecter\Entity\Address;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase\UniqueConstraintViolationException;
use Solean\CleanProspecter\Exception\UseCase\UseCaseException;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\Exception\UseCase\NotFoundException;

final class CreateOrganizationImpl extends AbstractUseCase implements CreateOrganization
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
        return ['ROLE_PROSPECT'];
    }

    public function execute(CreateOrganizationRequest $request, Presenter $presenter): ?object
    {
        $this->validateRequest($request);

        $organization = $this->buildOrganization($request);
        $this->joinToOwner($request, $organization);
        $this->joinToHoldingIfNeeded($request, $organization);

        $persisted = $this->create($organization);
        $response = $this->buildResponse($persisted);

        return $presenter->present($response);
    }

    private function validateRequest(CreateOrganizationRequest $request): void
    {
        if (!$request->getCorporateName() && !$request->getEmail()) {
            $msg = 'At least one is mandatory : corporate name or email';
            throw new UseCaseException('At least one is mandatory : corporate name or email', 412, null, ['*' => $msg]);
        }
    }

    private function buildOrganization(CreateOrganizationRequest $request): Organization
    {
        $organization = new Organization();
        if ($request->getLanguage()) {
            $organization->setLanguage($request->getLanguage());
        }
        if ($request->getEmail()) {
            $organization->setEmail($request->getEmail());
        }
        if ($request->getCorporateName()) {
            $organization->setCorporateName($request->getCorporateName());
        }
        if ($request->getForm()) {
            $organization->setForm($request->getForm());
        }
        if ($request->hasAddress()) {
            $organization->setAddress(Address::fromValues($request->getStreet(), $request->getPostalCode(), $request->getCity(), $request->getCountry()));
        }

        return $organization;
    }

    private function joinToOwner(CreateOrganizationRequest $request, Organization $organization): void
    {
        $owner = $this->organizationGateway->get($request->getOwnedBy());
        $organization->setOwnedBy($owner);
    }

    private function joinToHoldingIfNeeded(CreateOrganizationRequest $request, Organization $organization): void
    {
        if ($request->getHoldBy()) {
            try {
                $organization->setHoldBy($this->organizationGateway->get($request->getHoldBy()));
            } catch (Gateway\NotFoundException $e) {
                throw new NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $e, ['holdBy' => 'Holding not found']);
            }
        }
    }

    private function create(Organization $organization): Organization
    {
        try {
            $persisted = $this->organizationGateway->create($organization);
        } catch (Gateway\UniqueConstraintViolationException $e) {
            throw new UniqueConstraintViolationException('Email already used', 412, $e, ['email' => sprintf('Email "%s" already used', $organization->getEmail())]);
        }

        return $persisted;
    }

    private function buildResponse(Organization $persisted): CreateOrganizationResponse
    {
        $response = new CreateOrganizationResponse(
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

        return $response;
    }
}
