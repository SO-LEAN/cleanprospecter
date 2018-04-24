<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;


final class Address
{
    /**
     * @var string
     */
    private $street;
    /**
     * @var string
     */
    private $postalCode;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $country;

    private function __construct()
    {
    }

    public static function fromValues(string $street, string $postalCode, string $city, string $country)
    {
        $address = new Address();
        $address->street = $street;
        $address->postalCode = $postalCode;
        $address->city = $city;
        $address->country = $country;

        return $address;
    }
}
