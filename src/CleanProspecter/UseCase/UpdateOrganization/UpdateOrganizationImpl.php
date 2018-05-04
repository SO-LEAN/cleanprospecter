<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateOrganization;

use Solean\CleanProspecter\Entity\Address;
use Solean\CleanProspecter\Entity\File;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Exception\UseCase\NotFoundException;
use Solean\CleanProspecter\Exception\UseCase\UniqueConstraintViolationException;
use Solean\CleanProspecter\Exception\UseCase\UseCaseException;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;

final class UpdateOrganizationImpl extends AbstractUseCase implements UpdateOrganization
{
    /**
     * @var OrganizationGateway
     */
    private $organizationGateway;
    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var UserNotifier
     */
    private $userNotifier;

    public function __construct(OrganizationGateway $organizationGateway, Storage $storage, UserNotifier $userNotifier)
    {
        $this->organizationGateway = $organizationGateway;
        $this->storage = $storage;
        $this->userNotifier = $userNotifier;
    }

    public function canBeExecutedBy(): array
    {
        return ['ROLE_PROSPECTOR'];
    }

    public function execute(UpdateOrganizationRequest $request, UpdateOrganizationPresenter $presenter): ?object
    {
        $this->validateRequest($request);

        $organization = $this->alterOrganization($request, $this->organizationGateway->get($request->getId()));
        $this->joinToOwner($request, $organization);
        $this->joinToHoldingIfNeeded($request, $organization);

        $persisted = $this->update($organization);
        $response = $this->buildResponse($persisted);

        $this->notifySuccess('Organization updated !');

        return $presenter->present($response);
    }

    private function validateRequest(UpdateOrganizationRequest $request): void
    {
        if (!$request->getOwnedBy()) {
            $msg = 'Owner is missing';
            throw new UseCaseException('Owner is missing', 412, null, ['*' => $msg]);
        }

        if (!$request->getCorporateName() && !$request->getEmail()) {
            $msg = 'At least one is mandatory : corporate name or email';
            throw new UseCaseException($msg, 412, null, ['*' => $msg]);
        }
    }

    private function alterOrganization(UpdateOrganizationRequest $request, Organization $organization): Organization
    {

        $organization->setLanguage($request->getLanguage());
        $organization->setPhoneNumber($request->getPhoneNumber());
        $organization->setObservations($request->getObservations());
        $organization->setEmail($request->getEmail());
        $organization->setCorporateName($request->getCorporateName());
        $organization->setForm($request->getForm());

        if ($request->hasAddress()) {
            $organization->setAddress(Address::fromValues($request->getStreet(), $request->getPostalCode(), $request->getCity(), $request->getCountry()));
        } else {
            $organization->setAddress(null);
        }

        if ($request->getLogo()) {
            $organization->setLogo(File::fromValues($this->storage->add($request->getLogo()), $request->getLogo()->getExtension(), $request->getLogo()->getSize()));
        }

        return $organization;
    }

    private function joinToOwner(UpdateOrganizationRequest $request, Organization $organization): void
    {
        $owner = $this->organizationGateway->get($request->getOwnedBy());
        $organization->setOwnedBy($owner);
    }

    private function joinToHoldingIfNeeded(UpdateOrganizationRequest $request, Organization $organization): void
    {
        if ($request->getHoldBy()) {
            try {
                $organization->setHoldBy($this->organizationGateway->get($request->getHoldBy()));
            } catch (Gateway\NotFoundException $e) {
                throw new NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $e, ['holdBy' => 'Holding not found']);
            }
        } else {
            $organization->setHoldBy(null);
        }
    }

    private function update(Organization $organization): Organization
    {
        try {
            $persisted = $this->organizationGateway->update($organization->getId(), $organization);
        } catch (Gateway\UniqueConstraintViolationException $e) {
            throw new UniqueConstraintViolationException('Email already used', 412, $e, ['email' => sprintf('Email "%s" already used', $organization->getEmail())]);
        }

        return $persisted;
    }

    private function buildResponse(Organization $persisted): UpdateOrganizationResponse
    {
        $response = new UpdateOrganizationResponse(
            $persisted->getId(),
            $persisted->getOwnedBy()->getId(),
            $persisted->getPhoneNumber(),
            $persisted->getEmail(),
            $persisted->getLanguage(),
            $persisted->getCorporateName(),
            $persisted->getForm(),
            $persisted->getAddress() ? $persisted->getAddress()->getStreet() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getPostalCode() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getCity() : null,
            $persisted->getAddress() ? $persisted->getAddress()->getCountry() : null,
            $persisted->getObservations(),
            $persisted->getLogo() ? $persisted->getLogo()->getUrl() : null,
            $persisted->getLogo() ? $persisted->getLogo()->getExtension() : null,
            $persisted->getLogo() ? $persisted->getLogo()->getSize() : null,
            $persisted->getHoldBy() ? $persisted->getHoldBy()->getId() : null
        );

        return $response;
    }

    private function notifySuccess(string $msg)
    {
        $this->userNotifier->addSuccess($msg);
    }
}
