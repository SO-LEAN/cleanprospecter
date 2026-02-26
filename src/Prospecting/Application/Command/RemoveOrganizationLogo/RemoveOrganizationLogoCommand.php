<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Command\RemoveOrganizationLogo;

final readonly class RemoveOrganizationLogoCommand
{
    public function __construct(
        public string $organizationId,
    ) {}
}
