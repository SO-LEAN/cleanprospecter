<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Entity;

use Tests\Unit\Solean\Base\EntityTest;
use Solean\CleanProspecter\Entity\Email;

class EmailTest extends EntityTest
{
    public function target() : Email
    {
        return parent::target();
    }
}
