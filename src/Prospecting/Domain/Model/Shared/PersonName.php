<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Shared;

final readonly class PersonName
{
    private function __construct(
        public ?string $firstName,
        public ?string $lastName,
    ) {}

    public static function fromParts(?string $firstName, ?string $lastName): self
    {
        return new self($firstName, $lastName);
    }

    public function fullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }
}
