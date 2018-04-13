<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

abstract class Person extends Base
{
    /**
     * @var string
     */
    private $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    abstract public function getFullName(): string;
}
