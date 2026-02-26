<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Exception;

use RuntimeException;

class UserNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('User with #ID %s not found', $id));
    }
}
