<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Exception\UseCase;

use \Throwable;
use RuntimeException;

class UniqueConstraintViolationException extends UseCaseException
{
}