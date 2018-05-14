<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationResponse;

use Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationResponse\Organization;
use Tests\Unit\Solean\Base\Builder;

class OrganizationBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withData();
    }

    public function withData(): self
    {
        return $this
            ->with('id', 123)
            ->with('fullName', 'Organization Limited Company');
    }

    public function withAddress(): self
    {
        return $this
            ->with('country', 'EN')
            ->with('city', 'London')
            ->with('postalCode', 'SW1A 2AA')
            ;
    }

    public function withLogo(): self
    {
        return $this
            ->with('logo', 'http://url.net/image.png');
    }

    protected function getTargetClass(): string
    {
        return Organization::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
