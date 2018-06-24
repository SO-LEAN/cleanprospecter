<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use SplFileInfo;
use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationRequest;

class CreateOrganizationRequestBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData(): self
    {
        return $this
            ->with('phoneNumber', '03777666888')
            ->with('email', 'org@organization.com')
            ->with('language', 'EN')
            ->with('corporateName', 'Organization')
            ->with('form', 'Limited Company')
            ->with('type', 'Consulting')
            ->with('observations', 'observ.');
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

    public function withAddress(): self
    {
        return $this
            ->with('street', '10 Downing Street')
            ->with('postalCode', 'SW1A 2AA')
            ->with('city', 'London')
            ->with('country', 'EN');
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
        return CreateOrganizationRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
