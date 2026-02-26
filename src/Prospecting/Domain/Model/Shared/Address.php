<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Shared;

final readonly class Address
{
    private function __construct(
        public ?string $street,
        public ?string $postalCode,
        public ?string $city,
        public ?string $country,
    ) {}

    public static function create(?string $street, ?string $postalCode, ?string $city, ?string $country): self
    {
        return new self($street, $postalCode, $city, $country);
    }

    public function toSearchString(): string
    {
        return trim(sprintf('%s %s %s %s', $this->street, $this->postalCode, $this->city, $this->country));
    }
}
