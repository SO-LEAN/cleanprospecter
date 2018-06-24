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
     * @var int
     */
    private $totalPages;
    /**
     * @var array
     */
    private $content;

    public function __construct(int $number, int $total, int $totalPages, array $content)
    {
        $this->number = $number;
        $this->total = $total;
        $this->totalPages = $totalPages;
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

    public function getTotalPages()
    {
        return $this->totalPages;
    }
}
