<?php

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetOrganization;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationRequest;

class GetOrganizationRequestBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData(): self
    {
        return $this
            ->with('id', 777);
    }

    protected function getTargetClass(): string
    {
        return GetOrganizationRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
