<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetMyAccountInformation;

use Solean\CleanProspecter\Entity\User;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\GetMyAccountInformation\GetMyAccountInformationImpl;
use Solean\CleanProspecter\UseCase\GetMyAccountInformation\GetMyAccountInformationRequest;
use Solean\CleanProspecter\UseCase\GetMyAccountInformation\GetMyAccountInformationResponse;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\aUser;
use function Tests\Unit\Solean\Base\aFile;

class GetMyAccountInformationImplTest extends UseCaseTest
{
    public function target() : GetMyAccountInformationImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(UserGateway::class)->reveal(),
            $this->prophesy(OrganizationGateway::class)->reveal(),
        ];
    }

    public function testProspectorCanGetMyAccountInformation()
    {
        $this->assertArraySubset(['ROLE_USER'], $this->target()->canBeExecutedBy());
    }

    public function testExecuteOnRegular()
    {
        $request = new GetMyAccountInformationRequest();
        $user = aUser()
            ->withId()
            ->build();
        $organization = anOrganization()
            ->withId()
            ->build();
        $expectedResponse = aResponse()->build();

        $this->mockUseCaseConsumer();
        $this->mockOrganization($organization);
        $this->mockUser($user);

        /**
         * @var GetMyAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithOrganizationLogo()
    {
        $request = new GetMyAccountInformationRequest();
        $user = aUser()
            ->withId()
            ->build();
        $organization = anOrganization()
            ->withId()
            ->with('logo', aFile()->withImageData())
            ->build();

        $expectedResponse = aResponse()
            ->withOrganizationLogo()
            ->build();

        $this->mockUseCaseConsumer();
        $this->mockOrganization($organization);
        $this->mockUser($user);

        /**
         * @var GetMyAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithUserPicture()
    {
        $request = new GetMyAccountInformationRequest();
        $user = aUser()
            ->withId()
            ->with('picture', aFile()->withImageData())
            ->build();
        $organization = anOrganization()
            ->withId()
            ->build();

        $expectedResponse = aResponse()
            ->withPicture()
            ->build();

        $this->mockUseCaseConsumer();
        $this->mockOrganization($organization);
        $this->mockUser($user);

        /**
         * @var GetMyAccountInformationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testThrowAnUseCaseNotFoundExceptionIfOrganizationNotFoundInGateway()
    {
        $request = new GetMyAccountInformationRequest();
        $gatewayException = new Gateway\NotFoundException();

        $this->mockUser(aUser()->build());
        $this->mockUseCaseConsumer();
        $this->prophesy(OrganizationGateway::class)->get(2)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Organization with #ID %d not found', 2), 404, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter(), $this->prophesy(UseCaseConsumer::class)->reveal());
    }

    public function testThrowAnUseCaseNotFoundExceptionIfUserNotFoundInGateway()
    {
        $request = new GetMyAccountInformationRequest();
        $gatewayException = new Gateway\NotFoundException();

        $this->mockUseCaseConsumer();
        $this->prophesy(UserGateway::class)->get(1)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('User with #ID %d not found', 1), 404, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter(), $this->prophesy(UseCaseConsumer::class)->reveal());
    }

    private function mockUseCaseConsumer(): void
    {
        $this->prophesy(UseCaseConsumer::class)->getUserId()->willReturn(1);
        $this->prophesy(UseCaseConsumer::class)->getOrganizationId()->willReturn(2);
    }

    private function mockOrganization(Organization $organization): void
    {
        $this->prophesy(OrganizationGateway::class)->get(2)->shouldBeCalled()->willReturn($organization);
    }

    private function mockUser(User $user): void
    {
        $this->prophesy(UserGateway::class)->get(1)->shouldBeCalled()->willReturn($user);
    }
}

function aResponse()
{
    return new GetMyAccountInformationResponseBuilder();
}
