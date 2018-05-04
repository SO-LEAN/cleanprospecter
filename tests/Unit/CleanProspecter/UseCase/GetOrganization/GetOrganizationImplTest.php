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

        $this->assertResponseEquals($expectedResponse, $response);
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

        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertOwnedByEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithAddress()
    {
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->build();
        $expectedResponse =  aGetOrganizationResponse()
            ->withRegularAddress()
            ->build();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertAddressEquals($expectedResponse, $response);
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

        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertLogoEquals($expectedResponse, $response);
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

        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertHoldByEquals($expectedResponse, $response);
    }

    public function testExecuteOnFullFilled()
    {
        $request = aGetOrganizationRequest()->build();
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->ownedBy(anOrganization()->withCreatorData())
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

        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertOwnedByEquals($expectedResponse, $response);
        $this->assertAddressEquals($expectedResponse, $response);
        $this->assertHoldByEquals($expectedResponse, $response);
        $this->assertLogoEquals($expectedResponse, $response);
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

    private function assertResponseEquals(GetOrganizationResponse $expectedResponse, GetOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getId(), $response->getId());
        $this->assertEquals($expectedResponse->getPhoneNumber(), $response->getPhoneNumber());
        $this->assertEquals($expectedResponse->getEmail(), $response->getEmail());
        $this->assertEquals($expectedResponse->getLanguage(), $response->getLanguage());
        $this->assertEquals($expectedResponse->getCorporateName(), $response->getCorporateName());
        $this->assertEquals($expectedResponse->getForm(), $response->getForm());
        $this->assertEquals($expectedResponse->getObservations(), $response->getObservations());
    }

    private function assertOwnedByEquals(GetOrganizationResponse $expectedResponse, GetOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getOwnedBy(), $response->getOwnedBy());
    }

    private function assertHoldByEquals(GetOrganizationResponse $expectedResponse, GetOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getHoldBy(), $response->getHoldBy());
    }

    private function assertAddressEquals(GetOrganizationResponse $expectedResponse, GetOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getStreet(), $response->getStreet());
        $this->assertEquals($expectedResponse->getPostalCode(), $response->getPostalCode());
        $this->assertEquals($expectedResponse->getCity(), $response->getCity());
        $this->assertEquals($expectedResponse->getCountry(), $response->getCountry());
    }

    private function assertLogoEquals(GetOrganizationResponse $expectedResponse, GetOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getLogoUrl(), $response->getLogoUrl());
        $this->assertEquals($expectedResponse->getLogoExtension(), $response->getLogoExtension());
        $this->assertEquals($expectedResponse->getLogoSize(), $response->getLogoSize());
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
