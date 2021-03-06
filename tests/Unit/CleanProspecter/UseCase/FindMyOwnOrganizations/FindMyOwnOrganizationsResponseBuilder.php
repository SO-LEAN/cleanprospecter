<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsResponse;

class FindMyOwnOrganizationsResponseBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData(): self
    {
        return $this
            ->with('currentPage', 1)
            ->with('total', 25)
            ->with('totalPages', 3)
            ;
    }

    protected function getTargetClass(): string
    {
        return FindMyOwnOrganizationsResponse::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
