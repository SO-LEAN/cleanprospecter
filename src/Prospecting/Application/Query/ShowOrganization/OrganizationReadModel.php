<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowOrganization;

final readonly class OrganizationReadModel
{
    public function __construct(
        public string $id,
        public ?string $ownerId,
        public ?string $corporateName,
        public ?string $email,
        public ?string $phoneNumber,
        public ?string $language,
        public ?string $form,
        public ?string $type,
        public ?string $observations,
        public ?string $street,
        public ?string $postalCode,
        public ?string $city,
        public ?string $country,
        public ?float $longitude,
        public ?float $latitude,
        public ?string $logoUrl,
        public ?string $logoExtension,
        public ?int $logoSize,
        public ?string $holdingId,
    ) {}
}
