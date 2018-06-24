<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\RefreshUser;

interface RefreshUserPresenter
{
    public function present(RefreshUserResponse $response);
}
