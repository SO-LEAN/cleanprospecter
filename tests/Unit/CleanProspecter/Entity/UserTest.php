<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Entity;

use Solean\CleanProspecter\Entity\User;
use Tests\Unit\Solean\Base\EntityTest;

class UserTest extends EntityTest
{
    public function target() : User
    {
        return parent::target();
    }

    protected function ignoreSetters()
    {
        return ['setPicture', 'setEmail'];
    }
}
