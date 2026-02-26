<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Shared;

use InvalidArgumentException;

final readonly class Email
{
    public string $value;

    private function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('Email "%s" is not valid', $value));
        }
        $this->value = $value;
    }

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
