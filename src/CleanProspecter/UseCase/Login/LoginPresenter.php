<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\Login;

interface LoginPresenter
{
    public function present(LoginResponse $response);
}
