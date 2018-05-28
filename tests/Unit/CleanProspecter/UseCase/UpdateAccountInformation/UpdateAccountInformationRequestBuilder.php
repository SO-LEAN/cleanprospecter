<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateAccountInformation;

use SplFileInfo;
use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\UpdateAccountInformation\UpdateAccountInformationRequest;

class UpdateAccountInformationRequestBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withData();
    }

    public function withData(): self
    {
        return $this
            ->with('userName', 'login')
            ->with('password', 'password')
            ->with('salt', 'salt')
            ->with('roles', ['ROLE'])
            ->with('language', 'FR')
            ->with('firstName', 'Mike')
            ->with('lastName', 'Myers')
            ->with('phoneNumber', '0101010101')
            ->with('email', 'user@user.com')

            ->with('organizationCorporateName', 'Organization')
            ->with('organizationForm', 'Limited Company');
    }

    public function withNewData(): self
    {
        return $this
            ->with('userName', 'new login')
            ->with('password', 'new password')
            ->with('salt', 'salt')
            ->with('language', 'LU')
            ->with('firstName', 'New Mike')
            ->with('lastName', 'New Myers')
            ->with('phoneNumber', '0199999999')
            ->with('email', 'user@new-new-user.com')
            ->with('organizationCorporateName', 'New Organization')
            ->with('organizationForm', 'SARL');
    }

    public function withOrganizationLogo(SplFileInfo $logo): self
    {
        return $this
            ->with('organizationLogo', $logo);
    }

    public function withPicture(SplFileInfo $picture): self
    {
        return $this
            ->with('picture', $picture);
    }

    protected function getTargetClass(): string
    {
        return UpdateAccountInformationRequest::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
