<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway\GeoLocation;

class GeoPointResponse
{
    /**
     * @var string
     */
    private $address;
    /**
     * @var float
     */
    private $longitude;
    /**
     * @var float
     */
    private $latitude;
    /**
     * @var bool
     */
    private $succeeded;

    public function __construct(string $address, float $longitude, float $latitude, $succeeded = true)
    {
        $this->address = $address;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->succeeded = $succeeded;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function isSucceeded(): bool
    {
        return $this->succeeded;
    }
}
