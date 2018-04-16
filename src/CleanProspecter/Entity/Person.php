<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

abstract class Person extends Base
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $country;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    abstract public function getFullName(): string;
}
