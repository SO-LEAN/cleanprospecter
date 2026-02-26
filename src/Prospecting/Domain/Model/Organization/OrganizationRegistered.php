<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Organization;

use DateTimeImmutable;
use Solean\Prospecting\Domain\Event\DomainEvent;

final readonly class OrganizationRegistered implements DomainEvent
{
    public DateTimeImmutable $occurredOn;

    public function __construct(
        public OrganizationId $organizationId,
        ?DateTimeImmutable $occurredOn = null,
    ) {
        $this->occurredOn = $occurredOn ?? new DateTimeImmutable();
    }
}
