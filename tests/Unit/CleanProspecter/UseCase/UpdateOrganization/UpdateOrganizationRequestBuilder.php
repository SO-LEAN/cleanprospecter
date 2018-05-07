<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateOrganization;

use SplFileInfo;
use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationRequest;

class UpdateOrganizationRequestBuilder extends Builder
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
            ->with('language', 'BE')
            ->with('phoneNumber', '111111111')
            ->with('email', 'org@new-organization.com')
            ->with('corporateName', 'New Organization')
            ->with('form', 'SARL')
            ->with('observations', 'new observ.');
    }

    public function hold(): self
    {
        return $this
            ->with('holdBy', 456);
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

    public function missingMandatoryData(): self
    {
        return $this
            ->with('email', null)
            ->with('corporateName', null);
    }

    public function withLogo(SplFileInfo $logo): self
    {
        return $this
            ->with('logo', $logo);
    }

    protected function getTargetClass(): string
    {
        return UpdateOrganizationRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
