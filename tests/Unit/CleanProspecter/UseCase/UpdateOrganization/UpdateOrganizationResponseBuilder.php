<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateOrganization;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationResponse;

class UpdateOrganizationResponseBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withData()->withId();
    }

    public function withId(): self
    {
        return $this->with('id', 123);
    }

    public function named(): self
    {
        return $this->with('corporateName', 'Organization');
    }

    public function withData(): self
    {
        return $this
            ->with('phoneNumber', '03777666888')
            ->with('email', 'org@organization.com')
            ->with('language', 'EN')
            ->with('corporateName', 'Organization')
            ->with('form', 'Limited Company')
            ->with('observations', 'observ.');
    }

    public function withNewData(): self
    {
        return $this
            ->with('id', 123)
            ->with('ownedBy', 777)
            ->with('language', 'BE')
            ->with('phoneNumber', '111111111')
            ->with('email', 'org@new-organization.com')
            ->with('corporateName', 'New Organization')
            ->with('form', 'SARL')
            ->with('observations', 'new observ.');
    }

    public function withAddress(): self
    {
        return $this
            ->with('street', '10 Downing Street')
            ->with('postalCode', 'SW1A 2AA')
            ->with('city', 'London')
            ->with('country', 'EN');
    }

    public function withNewAddress(): self
    {
        return $this
            ->with('street', '20 avenue du Neuhof')
            ->with('postalCode', '67100')
            ->with('city', 'Strasbourg')
            ->with('country', 'FR')
            ->with('longitude', 7.7663456)
            ->with('latitude', 48.5554971);
    }

    public function withUnLocatableAddress(): self
    {
        return $this
            ->with('street', '20 avenue du Not found')
            ->with('postalCode', '67100')
            ->with('city', 'Not found')
            ->with('country', 'FR')
            ->with('longitude', null)
            ->with('latitude', null);
    }

    public function ownedByCreator(): self
    {
        return $this
            ->with('ownedBy', 777);
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
        return UpdateOrganizationResponse::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
