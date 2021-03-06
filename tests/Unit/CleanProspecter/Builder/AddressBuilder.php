<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Builder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Entity\Address;

class AddressBuilder extends Builder
{

    public function __construct()
    {
        parent::__construct();
        $this->withData();
    }

    public function withData()
    {
        return $this
            ->with('street', '10 Downing Street')
            ->with('postalCode', 'SW1A 2AA')
            ->with('city', 'London')
            ->with('country', 'EN');
    }

    public function withNewData()
    {
        return $this
            ->with('street', '20 avenue du Neuhof')
            ->with('postalCode', '67100')
            ->with('city', 'Strasbourg')
            ->with('country', 'FR');
    }

    public function withUnLocatableAddress(): self
    {
        return $this
            ->with('street', '20 avenue du Not found')
            ->with('postalCode', '67100')
            ->with('city', 'Not found')
            ->with('country', 'FR');
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
