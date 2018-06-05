<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Entity;

use Tests\Unit\Solean\Base\EntityTest;
use Solean\CleanProspecter\Entity\Call;

class CallTest extends EntityTest
{
    public function target() : Call
    {
        return parent::target();
    }
}
