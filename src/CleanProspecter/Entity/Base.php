<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

abstract class Base
{
    private $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}