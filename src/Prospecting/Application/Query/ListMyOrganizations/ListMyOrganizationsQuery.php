<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ListMyOrganizations;

final readonly class ListMyOrganizationsQuery
{
    public function __construct(
        public string $ownerOrganizationId,
        public int $page = 1,
        public string $query = '',
        public int $maxPerPage = 20,
    ) {}
}
