<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Double;

use Solean\Prospecting\Application\Port\Notifier;

final class SpyNotifier implements Notifier
{
    /** @var string[] */
    public array $successes = [];
    /** @var string[] */
    public array $warnings = [];
    /** @var string[] */
    public array $errors = [];
    /** @var string[] */
    public array $infos = [];

    public function success(string $message): void
    {
        $this->successes[] = $message;
    }

    public function warning(string $message): void
    {
        $this->warnings[] = $message;
    }

    public function error(string $message): void
    {
        $this->errors[] = $message;
    }

    public function info(string $message): void
    {
        $this->infos[] = $message;
    }
}
