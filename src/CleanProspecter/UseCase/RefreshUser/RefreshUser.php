<?php
namespace Solean\CleanProspecter\UseCase\RefreshUser;

use Solean\CleanProspecter\UseCase\Presenter;

interface RefreshUser
{
    public function execute(RefreshUserRequest $request, Presenter $presenter): ?object;
}
