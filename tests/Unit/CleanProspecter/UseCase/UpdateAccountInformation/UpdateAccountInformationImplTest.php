<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateAccountInformation;

use Solean\CleanProspecter\Entity\User;
use Solean\CleanProspecter\Gateway\Entity\Transaction;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\UseCase\UpdateAccountInformation\UpdateAccountInformationImpl;
use Solean\CleanProspecter\UseCase\UpdateAccountInformation\UpdateAccountInformationResponse;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use SplFileInfo;
use Prophecy\Argument;
use function Tests\Unit\Solean\Base\aUser;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Entity\GeoPoint;
use Solean\CleanProspecter\Gateway\GeoLocation;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationRequest;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationImpl;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationResponse;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aGeoPoint;
use function Tests\Unit\Solean\Base\aFile;

class UpdateAccountInformationImplTest extends UseCaseTest
{
    public function target() : UpdateAccountInformationImpl
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

    public function testProspectorCanUpdateOrganization()
    {
        $this->assertArraySubset(['ROLE_USER'], $this->target()->canBeExecutedBy());
    }

    public function testExecute()
    {
        $request = aRequest()->build();
        $persistedOrganization = anOrganization()
            ->withId()
            ->build();
        $persistedUser = aUser()
            ->withId()
            ->build();
        $expected = aResponse()->build();

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser);
        /**
         * @var UpdateAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expected), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expected, $response);
    }

    public function testExecuteEmptyToFull()
    {
        $request = aRequest()
            ->withNewData()
            ->build();
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

        $expected = aResponse()
            ->withNewData()
            ->build();

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser, $alteredOrganization, $alteredUser);
        /**
         * @var UpdateAccountInformationResponse $response
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

        $expected = aResponse()
            ->withPicture()
            ->build();

        $file = $this->mockFile($alteredUser, 'picture');
        $this->mockStorage($file, $alteredUser, 'picture');

        $request = aRequest()
            ->withPicture($file)
            ->build();

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser, $alteredOrganization, $alteredUser);
        /**
         * @var UpdateAccountInformationResponse $response
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

        $expected = aResponse()
            ->withOrganizationLogo()
            ->build();

        $file = $this->mockFile($alteredOrganization, 'logo');
        $this->mockStorage($file, $alteredOrganization, 'logo');

        $request = aRequest()
            ->withOrganizationLogo($file)
            ->build();

        $this->mockTransaction();
        $this->mock($persistedOrganization, $persistedUser, $alteredOrganization, $alteredUser);
        /**
         * @var UpdateAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expected), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expected, $response);
    }

    public function testExecuteRollbackAndThrowExceptionOnUniqueConstraintViolationException()
    {
        $request = aRequest()
            ->build();
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
            ->addSuccess('User account information update !')
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

function aRequest()
{
    return new UpdateAccountInformationRequestBuilder();
}
function aResponse()
{
    return new UpdateAccountInformationResponseBuilder();
}
