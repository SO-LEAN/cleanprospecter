<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\EntityBuilder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Entity\GeoPoint;

class GeoPointBuilder extends Builder
{

    public function __construct()
    {
        parent::__construct();
        $this->withData();
    }

    public function withData()
    {
        return $this
            ->with('longitude', 7.7663456)
            ->with('latitude', 48.5554971)
            ;
    }

    public function notFound()
    {
        return $this
            ->with('longitude', 0)
            ->with('latitude', 0);
    }

    protected function getTargetClass(): string
    {
        return GeoPoint::class;
    }

    protected function getTargetType(): string
    {
        return 'vo';
    }
}
