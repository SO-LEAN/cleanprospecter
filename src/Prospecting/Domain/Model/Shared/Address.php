<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Shared;

final readonly class Address
{
    public function __construct(
        public ?string $street,
        public ?string $postalCode,
        public ?string $city,
        public ?string $country,
    ) {}

    public function toSearchString(): string
    {
        return trim(sprintf('%s %s %s %s', $this->street, $this->postalCode, $this->city, $this->country));
    }
}
