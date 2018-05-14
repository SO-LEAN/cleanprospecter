<?php

declare(strict_types = 1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindOrganization;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationRequest;

class FindOrganizationRequestBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData(): self
    {
        $this
            ->with('page', 1)
            ->with('maxByPage', 10)
            ->with('query', 'my query')
        ;
        
        return $this;
    }

    protected function getTargetClass(): string
    {
        return FindOrganizationRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
