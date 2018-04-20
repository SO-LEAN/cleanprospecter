<?php

namespace Solean\CleanProspecter\UseCase\Login;

use Solean\CleanProspecter\UseCase\Presenter;

interface Login
{
    public function execute(LoginRequest $request, Presenter $presenter): ?object;
}
