<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway;

use Solean\CleanProspecter\Gateway\GeoLocation\GeoPointResponse;

interface GeoLocation
{
    public function find(string $address): GeoPointResponse;
}
