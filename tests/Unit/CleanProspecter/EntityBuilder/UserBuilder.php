<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\EntityBuilder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Entity\User;

class UserBuilder extends Builder
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
            ->with('userName', 'login')
            ->with('salt', $salt)
            ->with('password', md5(sprintf('%s%s', 'password', $salt)))
            ->with('roles', ['ROLE'])
            ->with('language', 'FR');
    }

    protected function getTargetClass(): string
    {
        return User::class;
    }
}
