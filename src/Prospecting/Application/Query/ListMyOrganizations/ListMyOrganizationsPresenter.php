<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ListMyOrganizations;

interface ListMyOrganizationsPresenter
{
    /**
     * @param OrganizationSummaryReadModel[] $organizations
     */
    public function present(int $currentPage, int $total, int $totalPages, array $organizations): void;
}
