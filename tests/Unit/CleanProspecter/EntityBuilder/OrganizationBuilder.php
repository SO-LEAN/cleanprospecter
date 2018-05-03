<?php

namespace Tests\Unit\Solean\CleanProspecter\EntityBuilder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Entity\Organization;

class OrganizationBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData(): self
    {
        return $this
            ->with('language', 'EN')
            ->with('phoneNumber', '03777666888')
            ->with('email', 'org@organization.com')
            ->with('corporateName', 'Organization')
            ->with('form', 'Limited Company')
            ->with('observations', 'observ.');
    }

    public function withHoldingData(): self
    {
        return $this
            ->with('id', 456)
            ->with('language', 'LU')
            ->with('phoneNumber', '0999999999')
            ->with('email', 'org@organization-holding.com')
            ->with('corporateName', 'Organization holding')
            ->with('form', 'GMBH')
            ->with('observations', 'observ.');
    }

    public function withCreatorData(): self
    {
        return $this
            ->with('id', 777)
            ->with('language', 'FR')
            ->with('phoneNumber', '0999999999')
            ->with('email', 'org@organization.com')
            ->with('corporateName', 'Prospector Organization')
            ->with('form', 'Limited Company')
            ->with('observations', 'observ.');
    }

    public function persistedAsRegular(): self
    {
        return $this
            ->with('id', 123);
    }

    protected function getTargetClass(): string
    {
        return Organization::class;
    }
}
