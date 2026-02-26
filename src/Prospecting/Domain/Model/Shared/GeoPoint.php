<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Shared;

final readonly class GeoPoint
{
    public function __construct(
        public float $longitude,
        public float $latitude,
    ) {}
}
