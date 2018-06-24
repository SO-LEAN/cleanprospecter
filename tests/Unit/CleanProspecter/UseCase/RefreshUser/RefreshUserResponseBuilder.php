<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RefreshUser;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserResponse;

class RefreshUserResponseBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData()
    {
        $salt = 'salt';

        return $this
            ->with('id', 123)
            ->with('roles', ['ROLE'])
            ->with('userName', 'login')
            ->with('password', md5(sprintf('%s%s', 'password', $salt)))
            ->with('organizationId', 777)
            ;
    }

    protected function getTargetClass(): string
    {
        return RefreshUserResponse::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
