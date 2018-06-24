<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetOrganization;

use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationImpl;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationResponse;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aGeoPoint;
use function Tests\Unit\Solean\Base\aFile;

class GetOrganizationImplTest extends UseCaseTest
{
    public function target() : GetOrganizationImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(OrganizationGateway::class)->reveal(),
        ];
    }

    public function testProspectorCanGetOrganization()
    {
        $this->assertArraySubset(['ROLE_PROSPECTOR'], $this->target()->canBeExecutedBy());
    }

    public function testExecuteOnRegular()
    {
        $request = aRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->build();
        $expectedResponse = aResponse()->build();

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnRegularWithOwner()
    {
        $request = aRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();
        $expectedResponse = aResponse()
            ->ownedByCreator()
            ->build() ;

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnRegularWithAddress()
    {
        $request = aRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->with('geoPoint', aGeoPoint())
            ->build();
        $expectedResponse =  aResponse()
            ->withRegularAddress()
            ->build();

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnRegularWithLogo()
    {
        $request = aRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('logo', aFile()->withImageData())
            ->build();
        $expectedResponse = aResponse()
            ->withLogo()
            ->build();

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
        ;
    }

    public function testExecuteOnHold()
    {
        $request = aRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('holdBy', anOrganization()->withHoldingData())
            ->build();
        $expectedResponse = aResponse()
            ->hold()
            ->build();

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnFullFilled()
    {
        $request = aRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->ownedBy(anOrganization()->withCreatorData())
            ->with('geoPoint', aGeoPoint())
            ->with('logo', aFile()->withImageData())
            ->with('holdBy', anOrganization()->withHoldingData())
            ->build();
        $expectedResponse = aResponse()
            ->withRegularAddress()
            ->ownedByCreator()
            ->hold()
            ->withLogo()
            ->build();

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testThrowAnUseCaseNotFoundExceptionIfOrganizationNotFoundInGateway()
    {
        $request = aRequest()->build();
        $gatewayException = new Gateway\NotFoundException();

        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Organization with #ID %d not found', $request->getId()), 404, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    private function mock(Organization $persisted): void
    {
        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn($persisted);
    }
}

function aRequest()
{
    return new GetOrganizationRequestBuilder();
}
function aResponse()
{
    return new GetOrganizationResponseBuilder();
}
