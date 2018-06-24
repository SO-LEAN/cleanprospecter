<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

use Solean\CleanProspecter\Entity\File;
use Solean\CleanProspecter\Entity\Address;
use Solean\CleanProspecter\Exception\Entity\ValidationException;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\GeoLocation;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\Traits\UseCase\UserNotifierTrait;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Traits\UseCase\GeoLocalizeTrait;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\Exception\UseCase\UseCaseException;
use Solean\CleanProspecter\Exception\UseCase\NotFoundException;
use Solean\CleanProspecter\Exception\UseCase\UniqueConstraintViolationException;

final class CreateOrganizationImpl extends AbstractUseCase implements CreateOrganization
{
    use GeoLocalizeTrait;
    use UserNotifierTrait;
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

    public function __construct(OrganizationGateway $organizationGateway, Storage $storage, UserNotifier $userNotifier, GeoLocation $geoLocation)
    {
        $this->organizationGateway = $organizationGateway;
        $this->storage = $storage;
        $this->userNotifier = $userNotifier;
        $this->geoLocation = $geoLocation;
    }

    public function canBeExecutedBy(): array
    {
        return ['ROLE_PROSPECTOR'];
    }

    public function execute(CreateOrganizationRequest $request, CreateOrganizationPresenter $presenter): ?object
    {
        $this->validateRequest($request);

        $organization = $this->buildOrganization($request);
        $this->joinToOwner($request, $organization);
        $this->joinToHoldingIfNeeded($request, $organization);
        $this->geolocalize($organization);

        $this->validate($organization);
        $persisted = $this->create($organization);
        $response = $this->buildResponse($persisted);

        $this->notifySuccess('Organization created !');

        return $presenter->present($response);
    }

    private function validateRequest(CreateOrganizationRequest $request): void
    {
        if (!$request->getOwnedBy()) {
            throw new UseCaseException('Owner is missing', 412, null, ['ownedBy' => 'Owner is missing']);
        }
    }

    private function buildOrganization(CreateOrganizationRequest $request): Organization
    {
        $organization = new Organization();

        $organization->setLanguage($request->getLanguage());
        $organization->setPhoneNumber($request->getPhoneNumber());
        $organization->setObservations($request->getObservations());
        $organization->setEmail($request->getEmail());
        $organization->setCorporateName($request->getCorporateName());
        $organization->setForm($request->getForm());
        $organization->setType($request->getType());

        if ($request->hasAddress()) {
            $organization->setAddress(Address::fromValues($request->getStreet(), $request->getPostalCode(), $request->getCity(), $request->getCountry()));
        }

        if ($request->getLogo()) {
            $organization->setLogo(File::fromValues($this->storage->add($request->getLogo()), $request->getLogo()->getExtension(), $request->getLogo()->getSize()));
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

    private function validate(Organization $organization): void
    {
        try {
            $organization->validate();
        } catch (ValidationException $e) {
            throw new UseCaseException($e->getMessage(), 412, $e, [$e->getField() => $e->getMessage()]);
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
            $persisted->getPhoneNumber(),
            $persisted->getEmail(),
            $persisted->getLanguage(),
            $persisted->getCorporateName(),
            $persisted->getForm(),
            $persisted->getType(),
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

        return $response;
    }
}
