<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetOrganization;

use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Tests\Unit\Solean\CleanProspecter\Factory\OrganizationFactory;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationImpl;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationPresenter;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationResponse;

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
        $request = GetOrganizationRequestFactory::regular();
        $persisted =  OrganizationFactory::regular();
        $expectedResponse = GetOrganizationResponseFactory::regular();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertResponseEquals($persisted, $response);
        $this->assertEquals($persisted->getAddress()->getStreet(), $response->getStreet());
        $this->assertEquals($persisted->getAddress()->getPostalCode(), $response->getPostalCode());
        $this->assertEquals($persisted->getAddress()->getCity(), $response->getCity());
        $this->assertEquals($persisted->getAddress()->getCountry(), $response->getCountry());
    }

    public function testExecuteOnRegularWithoutAddress()
    {
        $request = GetOrganizationRequestFactory::withoutAddress();
        $persisted =  OrganizationFactory::withoutAddress();
        $expectedResponse = GetOrganizationResponseFactory::withoutAddress();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertResponseEquals($persisted, $response);
    }

    public function testExecuteOnHold()
    {
        $request = GetOrganizationRequestFactory::hold();
        $persisted = OrganizationFactory::hold();
        $expectedResponse = GetOrganizationResponseFactory::hold();

        $this->mock($persisted, $expectedResponse);

        /**
         * @var GetOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(GetOrganizationPresenter::class)->reveal());

        $this->assertEquals($persisted->getHoldBy()->getId(), $response->getHoldBy());
    }

    public function testThrowAnUseCaseNotFoundExceptionIfOrganizationNotFoundInGateway()
    {
        $request = GetOrganizationRequestFactory::regular();
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

    private function assertResponseEquals(Organization $persisted, GetOrganizationResponse $response): void
    {
        $this->assertEquals($persisted->getId(), $response->getId());
        $this->assertEquals($persisted->getPhoneNumber(), $response->getPhoneNumber());
        $this->assertEquals($persisted->getEmail(), $response->getEmail());
        $this->assertEquals($persisted->getLanguage(), $response->getLanguage());
        $this->assertEquals($persisted->getCorporateName(), $response->getCorporateName());
        $this->assertEquals($persisted->getForm(), $response->getForm());
        $this->assertEquals($persisted->getObservations(), $response->getObservations());
        $this->assertEquals($persisted->getOwnedBy()->getId(), $response->getOwnedBy());
    }
}
