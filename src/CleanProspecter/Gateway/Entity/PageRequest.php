<?php

namespace Solean\CleanProspecter\Gateway\Entity;

class PageRequest
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $query;

    /**
     * @var int
     */
    private $maxByPage;
    /**
     * @var array
     */
    private $filter;

    public function __construct(int $page, string $query, int $maxByPage, array $filter = [])
    {
        $this->page = $page;
        $this->query = $query;
        $this->maxByPage = $maxByPage;
        $this->filter = $filter;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getMaxByPage(): int
    {
        return $this->maxByPage;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }
}
