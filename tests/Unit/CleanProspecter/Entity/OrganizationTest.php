<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Entity;

use InvalidArgumentException;
use Tests\Unit\Solean\Base\EntityTest;
use Solean\CleanProspecter\Entity\Organization;

class OrganizationTest extends EntityTest
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

    protected function ignoreSetters()
    {
        return ['setAddress', 'setLogo', 'setGeoPoint', 'setEmail'];
    }
}
