<?php

declare(strict_types = 1);

namespace Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

final class FindMyOwnOrganizationsRequest
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

    public function __construct(int $page, string $query, int $maxByPage = 20)
    {
        $this->page = $page;
        $this->query = $query;
        $this->maxByPage = $maxByPage;
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
}
