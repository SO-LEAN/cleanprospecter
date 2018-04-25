<?php

namespace Tests\Unit\Solean\CleanProspecter\Entity;

use InvalidArgumentException;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Entity\Organization;

class OrganizationTest extends TestCase
{
    public function target() : Organization
    {
        return parent::target();
    }

    public function testInvalidArgumentExceptionIsThrownWhenEmailIsMalformed()
    {
        $this->expectExceptionObject(new InvalidArgumentException('Email "bademail#gmail.com" is not valid'));

        $this->target()->setEmail('bademail#gmail.com');
    }
}
