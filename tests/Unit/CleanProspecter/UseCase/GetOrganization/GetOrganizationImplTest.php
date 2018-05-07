<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetOrganization;

use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationImpl;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationResponse;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationPresenter;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aGeoPoint;
use function Tests\Unit\Solean\Base\aFile;

class GetOrganizationImplTest extends TestCase
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
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->build();
        $expectedResponse = aGetOrganizationResponse()->build();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithOwner()
    {
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();
        $expectedResponse = aGetOrganizationResponse()
            ->ownedByCreator()
            ->build() ;

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithAddress()
    {
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->with('geoPoint', aGeoPoint())
            ->build();
        $expectedResponse =  aGetOrganizationResponse()
            ->withRegularAddress()
            ->build();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithLogo()
    {
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('logo', aFile()->withImageData())
            ->build();
        $expectedResponse = aGetOrganizationResponse()
            ->withLogo()
            ->build();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnHold()
    {
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('holdBy', anOrganization()->withHoldingData())
            ->build();
        $expectedResponse = aGetOrganizationResponse()
            ->hold()
            ->build();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnFullFilled()
    {
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->ownedBy(anOrganization()->withCreatorData())
            ->with('geoPoint', aGeoPoint())
            ->with('logo', aFile()->withImageData())
            ->with('holdBy', anOrganization()->withHoldingData())
            ->build();
        $expectedResponse = aGetOrganizationResponse()
            ->withRegularAddress()
            ->ownedByCreator()
            ->hold()
            ->withLogo()
            ->build();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testThrowAnUseCaseNotFoundExceptionIfOrganizationNotFoundInGateway()
    {
        $request = aGetOrganizationRequest()->build();
        $gatewayException = new Gateway\NotFoundException();

        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Organization with #ID %d not found', $request->getId()), 404, $gatewayException));

        $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());
    }

    private function mock(Organization $persisted, GetOrganizationResponse $expectedResponse): void
    {
        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn($persisted);
        $this->prophesy(GetOrganizationPresenter::class)->present($expectedResponse)->shouldBeCalled()->willReturnArgument(0);
    }
}

function aGetOrganizationRequest()
{
    return new GetOrganizationRequestBuilder();
}
function aGetOrganizationResponse()
{
    return new GetOrganizationResponseBuilder();
}
