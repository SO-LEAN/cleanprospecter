<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Builder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Gateway\Entity\Page;

class PageBuilder extends Builder
{

    public function __construct()
    {
        parent::__construct();
        $this->withData();
    }

    public function withData()
    {
        return $this
            ->with('number', 1)
            ->with('total', 25)
            ->with('totalPages', 3);
    }

    protected function getTargetClass(): string
    {
        return Page::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
