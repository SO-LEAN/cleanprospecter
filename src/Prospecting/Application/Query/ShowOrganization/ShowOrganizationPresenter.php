<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowOrganization;

interface ShowOrganizationPresenter
{
    public function present(OrganizationReadModel $readModel): void;
}
