<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Double;

use Solean\Prospecting\Application\Port\TransactionManager;

final class FakeTransactionManager implements TransactionManager
{
    public bool $committed = false;
    public bool $rolledBack = false;

    public function transactional(callable $operation): mixed
    {
        try {
            $result = $operation();
            $this->committed = true;
            return $result;
        } catch (\Throwable $e) {
            $this->rolledBack = true;
            throw $e;
        }
    }
}
