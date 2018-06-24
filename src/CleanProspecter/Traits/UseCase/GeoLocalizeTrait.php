<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Traits\UseCase;

use Solean\CleanProspecter\Gateway\GeoLocation;
use Solean\CleanProspecter\Entity\GeoLocatable;
use Solean\CleanProspecter\Entity\GeoPoint;

trait GeoLocalizeTrait
{
    /**
     * @var GeoLocation
     */
    private $geoLocation;

    private function geoLocalize(GeoLocatable $entity)
    {
        if ($entity->getAddress()) {
            $address = sprintf('%s %s %s %s', $entity->getAddress()->getStreet(), $entity->getAddress()->getPostalCode(), $entity->getAddress()->getCity(), $entity->getAddress()->getCountry());
            $response = $this->geoLocation->find($address);
            if ($response->isSucceeded()) {
                $entity->setGeoPoint(GeoPoint::fromValues($response->getLongitude(), $response->getLatitude()));
            } else {
                $entity->setGeoPoint(null);
            }
        } else {
            $entity->setGeoPoint(null);
        }
    }
}
