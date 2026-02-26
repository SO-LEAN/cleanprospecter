<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Prospect;

final readonly class ProspectId
{
    public function __construct(
        public string $value,
    ) {}

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
