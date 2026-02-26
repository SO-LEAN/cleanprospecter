<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowMyAccount;

final readonly class AccountReadModel
{
    public function __construct(
        public string $userName,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $pictureUrl,
        public ?string $pictureExtension,
        public ?int $pictureSize,
        public ?string $phoneNumber,
        public ?string $email,
        public ?string $language,
        public ?string $organizationCorporateName,
        public ?string $organizationForm,
        public ?string $organizationLogoUrl,
        public ?string $organizationLogoExtension,
        public ?int $organizationLogoSize,
    ) {}
}
