<?php

namespace Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsResponse\Organization;

final class FindMyOwnOrganizationsResponse
{
    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $totalPages;

    /**
     * @var Organization[]
     */
    private $organizations;

    public function __construct(int $currentPage, int $total, int $totalPages, array $organizations)
    {
        $this->currentPage = $currentPage;
        $this->total = $total;
        $this->totalPages = $totalPages;
        $this->organizations = $organizations;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @return Organization[]
     */
    public function getOrganizations(): array
    {
        return $this->organizations;
    }
}
