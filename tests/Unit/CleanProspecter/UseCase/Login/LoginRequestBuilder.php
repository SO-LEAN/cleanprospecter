<?php

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\Login\LoginRequest;

class LoginRequestBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData()
    {
        return $this
            ->with('login', 'login')
            ->with('password', 'password');
    }

    public function withTypo()
    {
        return $this
            ->with('login', 'login')
            ->with('password', 'passwerd');
    }

    protected function getTargetClass(): string
    {
        return LoginRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
