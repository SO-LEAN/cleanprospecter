<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Command\RemoveOrganizationLogo;

use Solean\Prospecting\Application\Port\FileStorage;
use Solean\Prospecting\Application\Port\Notifier;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;

final class RemoveOrganizationLogoHandler
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
        private readonly FileStorage $fileStorage,
        private readonly Notifier $notifier,
    ) {}

    public function __invoke(RemoveOrganizationLogoCommand $command): void
    {
        $organization = $this->organizationRepository->ofId(OrganizationId::fromString($command->organizationId));

        $logo = $organization->logo();
        if ($logo !== null) {
            $this->fileStorage->remove($logo->url);
        }

        $organization->removeLogo();
        $this->organizationRepository->save($organization);
        $this->notifier->success('Organization logo was removed !');
    }
}
