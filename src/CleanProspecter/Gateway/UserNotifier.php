<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway;


interface UserNotifier
{
    public function addSuccess(string $message): void;
    public function addWarning(string $message): void;
    public function addError(string $message): void;
}
