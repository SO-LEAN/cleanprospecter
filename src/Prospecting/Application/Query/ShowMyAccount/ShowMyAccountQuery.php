<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowMyAccount;

final readonly class ShowMyAccountQuery
{
    public function __construct(
        public string $userId,
        public string $organizationId,
    ) {}
}
