<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

final class GeoPoint
{
    /**
     * @var float
     */
    private $longitude;
    /**
     * @var float
     */
    private $latitude;

    private function __construct()
    {
    }

    public static function fromValues(float $longitude, float $latitude)
    {
        $geoPoint = new GeoPoint();
        $geoPoint->longitude = $longitude;
        $geoPoint->latitude = $latitude;

        return $geoPoint;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }
}
