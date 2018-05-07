<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Exception\Entity;

use \Throwable;
use RuntimeException;

class ValidationException extends RuntimeException
{
    /**
     * @var string
     */
    private $field;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, string $field)
    {
        parent::__construct($message, $code, $previous);
        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
