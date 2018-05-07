<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway\Entity;

class Page
{
    /**
     * @var int
     */
    private $number;
    /**
     * @var int
     */
    private $total;
    /**
     * @var array
     */
    private $content;

    public function __construct(int $number, int $total, array $content)
    {
        $this->number = $number;
        $this->total = $total;
        $this->content = $content;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getContent(): array
    {
        return $this->content;
    }
}
