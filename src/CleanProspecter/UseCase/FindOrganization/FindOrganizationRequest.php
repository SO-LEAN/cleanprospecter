<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindOrganization;

final class FindOrganizationRequest
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $query;

    public function __construct(int $page, string $query)
    {
        $this->page = $page;
        $this->query = $query;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
