<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Tests\Unit\Solean\CleanProspecter\Factory\OrganizationFactory;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationImpl;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationResponse;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationPresenter;

class CreateOrganizationImplTest extends TestCase
{
    public function target() : CreateOrganizationImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(OrganizationGateway::class)->reveal(),
        ];
    }

    public function testCanCreateCreateOrganization()
    {
        $this->assertInstanceOf($this->getTargetClassName(), $this->target());
    }

    public function testProspectorCanCreateOrganization()
    {
        $this->assertArraySubset(['ROLE_PROSPECTOR'], $this->target()->canBeExecutedBy());
    }

    public function testExecuteOnRegular()
    {
        $request = CreateOrganizationRequestFactory::regular();
        $notPersisted = OrganizationFactory::notPersistedRegular();
        $persisted =  OrganizationFactory::regular();
        $expectedResponse = CreateOrganizationResponseFactory::regular();

        $this->mock($notPersisted, $persisted, $expectedResponse);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());

        $this->assertResponseEquals($persisted, $response);
        $this->assertEquals($persisted->getAddress()->getStreet(), $response->getStreet());
        $this->assertEquals($persisted->getAddress()->getPostalCode(), $response->getPostalCode());
        $this->assertEquals($persisted->getAddress()->getCity(), $response->getCity());
        $this->assertEquals($persisted->getAddress()->getCountry(), $response->getCountry());
    }

    public function testExecuteOnRegularWithoutAddress()
    {
        $request = CreateOrganizationRequestFactory::withoutAddress();
        $notPersisted = OrganizationFactory::notPersistedWithoutAddress();
        $persisted =  OrganizationFactory::withoutAddress();
        $expectedResponse = CreateOrganizationResponseFactory::withoutAddress();

        $this->mock($notPersisted, $persisted, $expectedResponse);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());

        $this->assertResponseEquals($persisted, $response);
    }

    public function testExecuteOnHold()
    {
        $request = CreateOrganizationRequestFactory::hold();
        $notPersisted = OrganizationFactory::notPersistedHold();
        $persisted = OrganizationFactory::hold();
        $expectedResponse = CreateOrganizationResponseFactory::hold();

        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willReturn(OrganizationFactory::holding());

        $this->mock($notPersisted, $persisted, $expectedResponse);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());

        $this->assertEquals($persisted->getHoldBy()->getId(), $response->getHoldBy());
    }

    public function testThrowAnUseCaseNotFoundExceptionIfHoldingNotFoundInGatewayDuringExecuteOnHold()
    {
        $request = CreateOrganizationRequestFactory::hold();
        $gatewayException = new Gateway\NotFoundException();

        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn(OrganizationFactory::creator());
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = CreateOrganizationRequestFactory::regular();
        $notPersisted = OrganizationFactory::notPersistedRegular();
        $gatewayException = new Gateway\UniqueConstraintViolationException();

        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn(OrganizationFactory::creator());
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\UniqueConstraintViolationException('Email already used', 412, $gatewayException));

        $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());
    }

    public function testThrowUseCaseExceptionIfMissingCorporateNameAndEmail()
    {
        $request = CreateOrganizationRequestFactory::missingMandatory();

        $this->expectExceptionObject(new UseCase\UseCaseException('At least one is mandatory : corporate name or email', 412));

        $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());
    }

    private function mock(Organization $notPersisted, Organization $persisted, CreateOrganizationResponse $expectedResponse): void
    {
        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn(OrganizationFactory::creator());
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willReturn($persisted);
        $this->prophesy(CreateOrganizationPresenter::class)->present($expectedResponse)->shouldBeCalled()->willReturnArgument(0);
    }

    private function assertResponseEquals(Organization $persisted, CreateOrganizationResponse $response): void
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
