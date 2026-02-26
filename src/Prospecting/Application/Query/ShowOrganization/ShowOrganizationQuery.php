<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowOrganization;

final readonly class ShowOrganizationQuery
{
    public function __construct(
        public string $organizationId,
    ) {}
}
