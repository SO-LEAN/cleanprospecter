<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ListMyOrganizations;

final readonly class OrganizationSummaryReadModel
{
    public function __construct(
        public string $id,
        public string $fullName,
        public ?string $city,
        public ?string $country,
        public ?string $postalCode,
        public ?string $logoUrl,
        public ?float $longitude,
        public ?float $latitude,
    ) {}
}
