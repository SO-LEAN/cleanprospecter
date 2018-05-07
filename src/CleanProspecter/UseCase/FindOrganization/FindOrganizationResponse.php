<?php

namespace Solean\CleanProspecter\UseCase\FindOrganization;

use Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationResponse\Organization;

final class FindOrganizationResponse
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
     * @var Organization[]
     */
    private $organizations;

    /**
     * FindOrganizationResponse constructor.
     * @param int $currentPage
     * @param int $total
     * @param Organization[] $organizations
     */
    public function __construct(int $currentPage, int $total, array $organizations)
    {
        $this->currentPage = $currentPage;
        $this->total = $total;
        $this->organizations = $organizations;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return Organization[]
     */
    public function getOrganizations(): array
    {
        return $this->organizations;
    }
}
