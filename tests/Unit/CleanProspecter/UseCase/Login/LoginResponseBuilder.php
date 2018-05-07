<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\Login\LoginResponse;

class LoginResponseBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData()
    {
        return $this
            ->with('roles', ['ROLE'])
            ->with('userName', 'login')
            ->with('password', 'password')
            ;
    }

    protected function getTargetClass(): string
    {
        return LoginResponse::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
