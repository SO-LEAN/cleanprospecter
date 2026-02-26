<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowMyAccount;

interface ShowMyAccountPresenter
{
    public function present(AccountReadModel $readModel): void;
}
