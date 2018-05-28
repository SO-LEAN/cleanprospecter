<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateAccountInformation;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\UseCase\UpdateAccountInformation\UpdateAccountInformationResponse;

class UpdateAccountInformationResponseBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withData();
    }

    public function withData(): self
    {
        $salt = 'salt';
        return $this
            ->with('userName', 'login')
            ->with('password', md5(sprintf('%s%s', 'password', $salt)))
            ->with('salt', $salt)
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
        $salt = 'salt';
        return $this
            ->with('userName', 'new login')
            ->with('password', md5(sprintf('%s%s', 'new password', $salt)))
            ->with('salt', $salt)
            ->with('language', 'LU')
            ->with('firstName', 'New Mike')
            ->with('lastName', 'New Myers')
            ->with('phoneNumber', '0199999999')
            ->with('email', 'user@new-new-user.com')
            ->with('organizationCorporateName', 'New Organization')
            ->with('organizationForm', 'SARL');
    }

    public function withOrganizationLogo(): self
    {
        return $this
            ->with('organizationLogoUrl', 'http://url.net/image.png')
            ->with('organizationLogoExtension', 'png')
            ->with('organizationLogoSize', 2500);
    }

    public function withPicture(): self
    {
        return $this
            ->with('pictureUrl', 'http://url.net/image.png')
            ->with('pictureExtension', 'png')
            ->with('pictureLogoSize', 2500);
    }

    protected function getTargetClass(): string
    {
        return UpdateAccountInformationResponse::class;
    }

    protected function getTargetType(): string
    {
        return 'dto';
    }
}
