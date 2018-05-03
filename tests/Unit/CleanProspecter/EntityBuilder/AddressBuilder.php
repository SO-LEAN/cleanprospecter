<?php

namespace Tests\Unit\Solean\CleanProspecter\EntityBuilder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Entity\Address;

class AddressBuilder extends Builder
{

    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData()
    {
        return $this
            ->with('street', '10 Downing Street')
            ->with('postalCode', 'SW1A 2AA')
            ->with('city', 'London')
            ->with('country', 'EN');
    }

    protected function getTargetClass(): string
    {
        return Address::class;
    }

    protected function getTargetType(): string
    {
        return 'vo';
    }
}
