<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Organization;

use Solean\Prospecting\Domain\Event\DomainEvent;

trait RaiseDomainEvents
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    protected function raise(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return DomainEvent[] */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
