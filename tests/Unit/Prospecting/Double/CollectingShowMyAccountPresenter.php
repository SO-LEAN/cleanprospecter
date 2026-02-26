<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Double;

use Solean\Prospecting\Application\Query\ShowMyAccount\AccountReadModel;
use Solean\Prospecting\Application\Query\ShowMyAccount\ShowMyAccountPresenter;

final class CollectingShowMyAccountPresenter implements ShowMyAccountPresenter
{
    public ?AccountReadModel $readModel = null;

    public function present(AccountReadModel $readModel): void
    {
        $this->readModel = $readModel;
    }
}
