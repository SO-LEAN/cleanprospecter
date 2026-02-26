<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Double;

use Solean\Prospecting\Application\Query\ListMyOrganizations\ListMyOrganizationsPresenter;

final class CollectingListMyOrganizationsPresenter implements ListMyOrganizationsPresenter
{
    public int $currentPage = 0;
    public int $total = 0;
    public int $totalPages = 0;
    public array $organizations = [];

    public function present(int $currentPage, int $total, int $totalPages, array $organizations): void
    {
        $this->currentPage = $currentPage;
        $this->total = $total;
        $this->totalPages = $totalPages;
        $this->organizations = $organizations;
    }
}
