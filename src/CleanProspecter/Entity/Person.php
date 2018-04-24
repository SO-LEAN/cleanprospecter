<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

use InvalidArgumentException;

abstract class Person extends Base
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $language;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('Email "%s" is not valid', $email));
        }
        $this->email = $email;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    abstract public function getFullName(): string;
}
