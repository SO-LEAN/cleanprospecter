<?php

namespace Solean\CleanProspecter\UseCase\Login;

interface Login
{
    public function execute(LoginRequest $request, LoginPresenter $presenter);
}
