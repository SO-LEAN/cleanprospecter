<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateMyAccountInformation;

use SplFileInfo;
use Solean\CleanProspecter\Entity\User;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\Gateway\Entity\Transaction;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\UpdateMyAccountInformation\UpdateMyAccountInformationImpl;
use Solean\CleanProspecter\UseCase\UpdateMyAccountInformation\UpdateMyAccountInformationRequest;
use Solean\CleanProspecter\UseCase\UpdateMyAccountInformation\UpdateMyAccountInformationResponse;

use function Tests\Unit\Solean\Base\aUser;
use function Tests\Unit\Solean\Base\aFile;
use function Tests\Unit\Solean\Base\anOrganization;

class UpdateMyAccountInformationImplTest extends UseCaseTest
{
    public function target() : UpdateMyAccountInformationImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(OrganizationGateway::class)->reveal(),
            $this->prophesy(UserGateway::class)->reveal(),
            $this->prophesy(Transaction::class)->reveal(),
            $this->prophesy(Storage::class)->reveal(),
            $this->prophesy(UserNotifier::class)->reveal(),
        ];
    }

    public function testUserCanUpdateOrganization()
    {
        $this->assertArraySubset(['ROLE_USER'], $this->target()->canBeExecutedBy());
    }

    public function testExecute()
    {
        $request = new UpdateMyAccountInformationRequest('login', 'password', 'Mike', 'Myers', null, '0101010101', 'user@user.com', 'FR', 'Organization', 'Limited Company', null);
        $persistedOrganization = anOrganization()
            ->withId()
            ->build();
        $persistedUser = aUser()
            ->withId()
            ->build();
        $expected = new UpdateMyAccountInformationResponse('login', 'Mike', 'Myers', null, null, null, '0101010101', 'user@user.com', 'FR', 'Organization', 'Limited Company', null, null, null);

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser);
        /**
         * @var UpdateMyAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expected), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expected, $response);
    }

    public function testExecuteEmptyToFull()
    {
        $request = new UpdateMyAccountInformationRequest('new login', 'new password', 'New Mike', 'New Myers', null, '0199999999', 'user@new-new-user.com', 'LU', 'New Organization', 'SARL', null);
        $persistedOrganization = anOrganization()
            ->withId()
            ->build();
        $alteredOrganization = anOrganization()
            ->withId()
            ->withNewDataFromAccountUpdate()
            ->build();

        $persistedUser = aUser()
            ->withId()
            ->build();
        $alteredUser= aUser()
            ->withId()
            ->withNewData()
            ->build();

        $expected = new UpdateMyAccountInformationResponse('new login', 'New Mike', 'New Myers', null, null, null, '0199999999', 'user@new-new-user.com', 'LU', 'New Organization', 'SARL', null, null, null);

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser, $alteredOrganization, $alteredUser);
        /**
         * @var UpdateMyAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expected), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expected, $response);
    }

    public function testExecuteWithPicture()
    {
        $persistedOrganization = anOrganization()
            ->withId()
            ->build();

        $alteredOrganization = anOrganization()
            ->withId()
            ->with('language', 'FR')
            ->build();

        $persistedUser = aUser()
            ->withId()
            ->build();

        $alteredUser= aUser()
            ->withId()
            ->with('picture', aFile()->withImageData()->build())
            ->build();

        $expected = new UpdateMyAccountInformationResponse('login', 'Mike', 'Myers', 'http://url.net/image.png', 'png', 2500, '0101010101', 'user@user.com', 'FR', 'Organization', 'Limited Company', null, null, null);

        $file = $this->mockFile($alteredUser, 'picture');
        $this->mockStorage($file, $alteredUser, 'picture');

        $request = new UpdateMyAccountInformationRequest('login', 'password', 'Mike', 'Myers', $file, '0101010101', 'user@user.com', 'FR', 'Organization', 'Limited Company', null);

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser, $alteredOrganization, $alteredUser);
        /**
         * @var UpdateMyAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expected), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expected, $response);
    }

    public function testExecuteWithLogo()
    {
        $persistedOrganization = anOrganization()
            ->withId()
            ->build();

        $alteredOrganization = anOrganization()
            ->withId()
            ->with('language', 'FR')
            ->with('logo', aFile()->withImageData())
            ->build();

        $persistedUser = aUser()
            ->withId()
            ->build();

        $alteredUser= aUser()
            ->withId()
            ->build();

        $expected = new UpdateMyAccountInformationResponse('login', 'Mike', 'Myers', null, null, null, '0101010101', 'user@user.com', 'FR', 'Organization', 'Limited Company', 'http://url.net/image.png', 'png', 2500);

        $file = $this->mockFile($alteredOrganization, 'logo');
        $this->mockStorage($file, $alteredOrganization, 'logo');

        $request = new UpdateMyAccountInformationRequest('login', 'password', 'Mike', 'Myers', null, '0101010101', 'user@user.com', 'FR', 'Organization', 'Limited Company', $file);

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser, $alteredOrganization, $alteredUser);
        /**
         * @var UpdateMyAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expected), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expected, $response);
    }

    public function testExecuteRollbackAndThrowExceptionOnUniqueConstraintViolationException()
    {
        $request = new UpdateMyAccountInformationRequest('login', 'password', 'Mike', 'Myers', null, '0101010101', 'user@user.com', 'FR', 'Organization', 'Limited Company', null);
        $persistedOrganization = anOrganization()
            ->withId()
            ->missingMandatoryData()
            ->build();
        $persistedUser = aUser()
            ->withId()
            ->build();

        $this->prophesy(Transaction::class)->begin()->shouldBeCalled();
        $this->prophesy(Transaction::class)->rollback()->shouldBeCalled();
        $this->prophesy(UseCaseConsumer::class)
            ->getOrganizationId()
            ->willReturn(123);
        $this->prophesy(UseCaseConsumer::class)
            ->getUserId()
            ->shouldBeCalled()
            ->willReturn(123);
        $this->prophesy(OrganizationGateway::class)
            ->get(123)
            ->shouldBeCalled()
            ->willReturn($persistedOrganization);
        $this->prophesy(UserGateway::class)
            ->get(123)
            ->shouldBeCalled()
            ->willReturn($persistedUser);

        $this->prophesy(UserGateway::class)
            ->update(123, $persistedUser)
            ->shouldBeCalled()
            ->willThrow(new Gateway\UniqueConstraintViolationException());

        $this->expectExceptionObject(new UseCase\UniqueConstraintViolationException('Email already used', 412, new Gateway\UniqueConstraintViolationException()));


        $this->target()->execute($request, $this->getMockedPresenter(), $this->prophesy(UseCaseConsumer::class)->reveal());
    }

    private function mockTransaction()
    {
        $this->prophesy(Transaction::class)->begin()->shouldBeCalled();
        $this->prophesy(Transaction::class)->rollback()->shouldNotBeCalled();
        $this->prophesy(Transaction::class)->commit()->shouldBeCalled();
    }
    private function mock(Organization $persistedOrganization, User $persistedUser, Organization $alteredOrganization = null, User $alteredUser = null): void
    {
        $this->prophesy(UseCaseConsumer::class)
            ->getOrganizationId()
            ->willReturn(123);
        $this->prophesy(UseCaseConsumer::class)
            ->getUserId()
            ->shouldBeCalled()
            ->willReturn(123);
        $this->prophesy(OrganizationGateway::class)
            ->get(123)
            ->shouldBeCalled()
            ->willReturn($persistedOrganization);
        $this->prophesy(UserGateway::class)
            ->get(123)
            ->shouldBeCalled()
            ->willReturn($persistedUser);
        $this->prophesy(OrganizationGateway::class)
            ->update($persistedOrganization->getId(), $alteredOrganization ?? $persistedOrganization)
            ->shouldBeCalled()
            ->willReturn($alteredOrganization ?? $persistedOrganization);
        $this->prophesy(UserGateway::class)
            ->update($persistedOrganization->getId(), $alteredUser ?? $persistedUser)
            ->shouldBeCalled()
            ->willReturn($alteredUser ?? $persistedUser);
        $this->prophesy(UserNotifier::class)
            ->addSuccess('User account information updated !')
            ->shouldBeCalled();
    }

    private function mockFile($entity, $property): SplFileInfo
    {
        $getter = sprintf('get%s', ucfirst($property));

        $this->prophesy(SplFileInfo::class)->getSize()->shouldBeCalled()->willReturn($entity->$getter()->getSize());
        $this->prophesy(SplFileInfo::class)->getExtension()->shouldBeCalled()->willReturn($entity->$getter()->getExtension());

        return $this->prophesy(SplFileInfo::class)->reveal();
    }

    private function mockStorage($file, $entity, $property): void
    {
        $getter = sprintf('get%s', ucfirst($property));
        $this->prophesy(Storage::class)->add($file)->shouldBeCalled()->willReturn($entity->$getter()->getUrl());
    }
}
