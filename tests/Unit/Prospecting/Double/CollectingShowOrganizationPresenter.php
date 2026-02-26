<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Double;

use Solean\Prospecting\Application\Query\ShowOrganization\OrganizationReadModel;
use Solean\Prospecting\Application\Query\ShowOrganization\ShowOrganizationPresenter;

final class CollectingShowOrganizationPresenter implements ShowOrganizationPresenter
{
    public ?OrganizationReadModel $readModel = null;

    public function present(OrganizationReadModel $readModel): void
    {
        $this->readModel = $readModel;
    }
}
