<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Exception;

use RuntimeException;
use Throwable;

class ValidationException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 412,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
