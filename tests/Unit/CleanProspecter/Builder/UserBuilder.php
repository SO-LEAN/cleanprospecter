<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Builder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Entity\User;

class UserBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withId(): self
    {
        return $this->with('id', 123);
    }

    public function withRegularData()
    {
        $salt = 'salt';
        return $this
            ->with('id', 123)
            ->with('userName', 'login')
            ->with('password', md5(sprintf('%s%s', 'password', $salt)))
            ->with('salt', $salt)
            ->with('roles', ['ROLE'])
            ->with('language', 'FR')
            ->with('firstName', 'Mike')
            ->with('lastName', 'Myers')
            ->with('phoneNumber', '0101010101')
            ->with('email', 'user@user.com');
    }

    public function withNewData()
    {
        $salt = 'salt';
        return $this
            ->with('userName', 'new login')
            ->with('password', md5(sprintf('%s%s', 'new password', $salt)))
            ->with('salt', $salt)
            ->with('language', 'LU')
            ->with('firstName', 'New Mike')
            ->with('lastName', 'New Myers')
            ->with('phoneNumber', '0199999999')
            ->with('email', 'user@new-new-user.com');
    }

    protected function getTargetClass(): string
    {
        return User::class;
    }
}
