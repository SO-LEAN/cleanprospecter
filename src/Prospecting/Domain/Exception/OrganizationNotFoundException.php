<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Exception;

use RuntimeException;

class OrganizationNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Organization with #ID %s not found', $id));
    }
}
