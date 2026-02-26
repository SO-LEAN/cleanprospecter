<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Command\RegisterOrganization;

use Solean\Prospecting\Application\Port\FileStorage;
use Solean\Prospecting\Application\Port\GeoLocationService;
use Solean\Prospecting\Application\Port\Notifier;
use Solean\Prospecting\Domain\Exception\OrganizationNotFoundException;
use Solean\Prospecting\Domain\Exception\UniqueConstraintViolationException;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;
use Solean\Prospecting\Domain\Model\Shared\Address;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;

final class RegisterOrganizationHandler
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
        private readonly FileStorage $fileStorage,
        private readonly GeoLocationService $geoLocation,
        private readonly Notifier $notifier,
    ) {}

    public function __invoke(RegisterOrganizationCommand $command): void
    {
        $id = $this->organizationRepository->nextId();

        // Verify owner exists
        try {
            $this->organizationRepository->ofId(OrganizationId::fromString($command->ownerId));
        } catch (OrganizationNotFoundException) {
            throw new OrganizationNotFoundException(sprintf('Owner with #ID %s not found', $command->ownerId));
        }

        // Verify holding exists if specified
        if ($command->holdingId !== null) {
            try {
                $this->organizationRepository->ofId(OrganizationId::fromString($command->holdingId));
            } catch (OrganizationNotFoundException) {
                throw new OrganizationNotFoundException(sprintf('Holding with #ID %s not found', $command->holdingId));
            }
        }

        $address = $this->buildAddress($command);
        $logo = $this->buildLogo($command);

        $organization = Organization::register(
            id: $id,
            ownerId: OrganizationId::fromString($command->ownerId),
            corporateName: $command->corporateName,
            email: $command->email ? Email::fromString($command->email) : null,
            phoneNumber: $command->phoneNumber ? PhoneNumber::fromString($command->phoneNumber) : null,
            language: $command->language,
            form: $command->form,
            type: $command->type,
            observations: $command->observations,
            address: $address,
            logo: $logo,
            holdingId: $command->holdingId ? OrganizationId::fromString($command->holdingId) : null,
        );

        if ($address !== null) {
            $geoPoint = $this->geoLocation->locate($address->toSearchString());
            $organization->pinpoint($geoPoint);
        }

        $this->organizationRepository->save($organization);
        $this->notifier->success('Organization created !');
    }

    private function buildAddress(RegisterOrganizationCommand $command): ?Address
    {
        if ($command->street === null && $command->postalCode === null && $command->city === null && $command->country === null) {
            return null;
        }

        return Address::create($command->street, $command->postalCode, $command->city, $command->country);
    }

    private function buildLogo(RegisterOrganizationCommand $command): ?Logo
    {
        if ($command->logo === null) {
            return null;
        }

        $url = $this->fileStorage->store($command->logo);

        return Logo::create($url, $command->logo->getExtension(), $command->logo->getSize());
    }
}
