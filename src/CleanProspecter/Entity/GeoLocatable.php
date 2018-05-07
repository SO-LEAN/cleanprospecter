<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

interface GeoLocatable
{
    public function getAddress(): ?Address;
    public function setGeoPoint(?GeoPoint $geoPoint): void;
}
