<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Port;

use Solean\Prospecting\Domain\Model\Shared\GeoPoint;

interface GeoLocationService
{
    public function locate(string $address): ?GeoPoint;
}
