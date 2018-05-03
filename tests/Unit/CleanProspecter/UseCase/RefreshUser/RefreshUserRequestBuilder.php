<?php

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RefreshUser;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserRequest;

class RefreshUserRequestBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData()
    {
        return $this
            ->with('login', 'login');
    }

    protected function getTargetClass(): string
    {
        return RefreshUserRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
