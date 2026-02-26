<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Shared;

final readonly class GeoPoint
{
    private function __construct(
        public float $longitude,
        public float $latitude,
    ) {}

    public static function fromCoordinates(float $longitude, float $latitude): self
    {
        return new self($longitude, $latitude);
    }
}
