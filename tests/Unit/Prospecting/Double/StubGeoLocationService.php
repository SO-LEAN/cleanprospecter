<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Double;

use Solean\Prospecting\Application\Port\GeoLocationService;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;

final class StubGeoLocationService implements GeoLocationService
{
    private ?GeoPoint $result = null;

    public function willReturn(?GeoPoint $geoPoint): void
    {
        $this->result = $geoPoint;
    }

    public function locate(string $address): ?GeoPoint
    {
        return $this->result;
    }
}
