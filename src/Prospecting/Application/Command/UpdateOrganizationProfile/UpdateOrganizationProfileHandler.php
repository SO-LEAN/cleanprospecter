<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Command\UpdateOrganizationProfile;

use Solean\Prospecting\Application\Port\FileStorage;
use Solean\Prospecting\Application\Port\GeoLocationService;
use Solean\Prospecting\Application\Port\Notifier;
use Solean\Prospecting\Domain\Exception\OrganizationNotFoundException;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;
use Solean\Prospecting\Domain\Model\Shared\Address;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;

final class UpdateOrganizationProfileHandler
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
        private readonly FileStorage $fileStorage,
        private readonly GeoLocationService $geoLocation,
        private readonly Notifier $notifier,
    ) {}

    public function __invoke(UpdateOrganizationProfileCommand $command): void
    {
        $organizationId = new OrganizationId($command->organizationId);
        $organization = $this->organizationRepository->ofId($organizationId);

        // Verify holding exists if specified
        if ($command->holdingId !== null) {
            try {
                $this->organizationRepository->ofId(new OrganizationId($command->holdingId));
            } catch (OrganizationNotFoundException) {
                throw new OrganizationNotFoundException(sprintf('Holding with #ID %s not found', $command->holdingId));
            }
        }

        $address = $this->buildAddress($command);

        $organization->updateProfile(
            corporateName: $command->corporateName,
            email: $command->email ? new Email($command->email) : null,
            phoneNumber: $command->phoneNumber ? new PhoneNumber($command->phoneNumber) : null,
            language: $command->language,
            form: $command->form,
            type: $command->type,
            observations: $command->observations,
            address: $address,
            holdingId: $command->holdingId ? new OrganizationId($command->holdingId) : null,
        );

        if ($command->logo !== null) {
            $url = $this->fileStorage->store($command->logo);
            $organization->attachLogo(new Logo($url, $command->logo->getExtension(), $command->logo->getSize()));
        }

        if ($address !== null) {
            $geoPoint = $this->geoLocation->locate($address->toSearchString());
            $organization->pinpoint($geoPoint);
        }

        $this->organizationRepository->save($organization);
        $this->notifier->success('Organization updated !');
    }

    private function buildAddress(UpdateOrganizationProfileCommand $command): ?Address
    {
        if ($command->street === null && $command->postalCode === null && $command->city === null && $command->country === null) {
            return null;
        }

        return new Address($command->street, $command->postalCode, $command->city, $command->country);
    }
}
