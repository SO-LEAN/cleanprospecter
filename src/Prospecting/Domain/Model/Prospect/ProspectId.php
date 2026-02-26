<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Prospect;

final readonly class ProspectId
{
    private function __construct(
        public string $value,
    ) {}

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
