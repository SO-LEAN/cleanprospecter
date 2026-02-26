<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Command\UpdateOrganizationProfile;

use SplFileInfo;

final readonly class UpdateOrganizationProfileCommand
{
    public function __construct(
        public string $organizationId,
        public ?string $corporateName = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?string $language = null,
        public ?string $form = null,
        public ?string $type = null,
        public ?string $observations = null,
        public ?string $street = null,
        public ?string $postalCode = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?SplFileInfo $logo = null,
        public ?string $holdingId = null,
    ) {}
}
