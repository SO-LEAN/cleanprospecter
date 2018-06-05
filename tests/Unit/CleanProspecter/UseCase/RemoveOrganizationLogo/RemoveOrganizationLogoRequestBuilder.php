<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RemoveOrganizationLogo;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\RemoveOrganizationLogo\RemoveOrganizationLogoRequest;

class RemoveOrganizationLogoRequestBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData(): self
    {
        return $this->with('organizationId', 123);
    }

    protected function getTargetClass(): string
    {
        return RemoveOrganizationLogoRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
