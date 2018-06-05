<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Entity;

use Tests\Unit\Solean\Base\EntityTest;
use Solean\CleanProspecter\Entity\Prospect;

class ProspectTest extends EntityTest
{
    public function target() : Prospect
    {
        return parent::target();
    }

    protected function ignoreSetters()
    {
        return ['setEmail'];
    }
}
