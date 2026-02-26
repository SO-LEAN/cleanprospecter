<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Command\UpdateMyAccount;

use SplFileInfo;

final readonly class UpdateMyAccountCommand
{
    public function __construct(
        public string $userId,
        public string $organizationId,
        public string $userName,
        public ?string $password = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phoneNumber = null,
        public ?string $email = null,
        public ?string $language = null,
        public ?string $organizationCorporateName = null,
        public ?string $organizationForm = null,
        public ?SplFileInfo $picture = null,
        public ?SplFileInfo $organizationLogo = null,
    ) {}
}
