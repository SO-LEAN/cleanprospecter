<?php

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationResponse;

class CreateOrganizationResponseBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData(): self
    {
        return $this
            ->with('id', 123)
            ->with('ownedBy', 777)
            ->with('phoneNumber', '03777666888')
            ->with('email', 'org@organization.com')
            ->with('language', 'EN')
            ->with('corporateName', 'Organization')
            ->with('form', 'Limited Company')
            ->with('observations', 'observ.');
    }

    public function withRegularAddress(): self
    {
        return $this
            ->with('street', '10 Downing Street')
            ->with('postalCode', 'SW1A 2AA')
            ->with('city', 'London')
            ->with('country', 'EN');
    }

    public function hold(): self
    {
        return $this
            ->with('holdBy', 456);
    }

    public function withLogo(): self
    {
        return $this
            ->with('logoUrl', 'http://url.net/image.png')
            ->with('logoExtension', 'png')
            ->with('logoSize', 2500);
    }

    protected function getTargetClass(): string
    {
        return CreateOrganizationResponse::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
