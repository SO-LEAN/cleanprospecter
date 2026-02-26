<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Event;

interface DomainEventDispatcher
{
    public function dispatch(DomainEvent ...$events): void;
}
