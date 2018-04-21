<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Exception\UseCase;

use \Throwable;
use RuntimeException;

abstract class UseCaseException extends RuntimeException
{
    /**
     * @var array
     */
    private $requestErrors;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, array $requestErrors = [])
    {
        parent::__construct($message, $code, $previous);
        $this->requestErrors = $requestErrors;
    }

    /**
     * @return array
     */
    public function getRequestErrors() : array
    {
        return $this->requestErrors;
    }
}
