<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationRequest;
use Stdclass;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationImpl;
use Tests\Unit\Solean\CleanProspecter\Factory\OrganizationFactory;

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

    /**
     * @param CreateOrganizationRequest $request
     * @param Organization $notPersisted
     * @param Organization $persisted
     * @dataProvider provideExecute
     */
    public function testExecute(CreateOrganizationRequest $request, Organization $notPersisted, Organization $persisted)
    {
        $this->mock($notPersisted, $persisted);

        $this->target()->execute($request, $this->prophesy(Presenter::class)->reveal());
    }

    /**
     * @return array
     */
    public function provideExecute()
    {
        return [
            'on regular' => [CreateOrganizationRequestFactory::regular(), OrganizationFactory::notPersistedRegular(), OrganizationFactory::regular()],
            'on without address' => [CreateOrganizationRequestFactory::withoutAddress(), OrganizationFactory::notPersistedWithoutAddress(), OrganizationFactory::withoutAddress()],
        ];
    }

    public function testExecuteOnHold()
    {
        $request = CreateOrganizationRequestFactory::hold();
        $notPersisted = OrganizationFactory::notPersistedHold();
        $persisted = OrganizationFactory::hold();

        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willReturn(OrganizationFactory::holding());

        $this->mock($notPersisted, $persisted);

        $this->target()->execute($request, $this->prophesy(Presenter::class)->reveal());
    }

    public function testThrowAnUseCaseNotFoundExceptionIfHoldingNotFoundInGatewayDuringExecuteOnHold()
    {
        $request = CreateOrganizationRequestFactory::hold();
        $gatewayException = new Gateway\NotFoundException();

        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->prophesy(Presenter::class)->reveal());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = CreateOrganizationRequestFactory::regular();
        $notPersisted = OrganizationFactory::notPersistedRegular();
        $gatewayException = new Gateway\UniqueConstraintViolationException();

        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\UniqueConstraintViolationException('Email already used', 412, $gatewayException));

        $this->target()->execute($request, $this->prophesy(Presenter::class)->reveal());
    }

    public function testThrowUseCaseExceptionIfMissingCorporateNameAndEmail()
    {
        $request = CreateOrganizationRequestFactory::missingMandatory();

        $this->expectExceptionObject(new UseCase\UseCaseException('At least one is mandatory : corporate name or email', 412));

        $this->target()->execute($request, $this->prophesy(Presenter::class)->reveal());
    }

    /**t
     * @param Organization $notPersisted
     * @param Organization $persisted
     */
    private function mock(Organization $notPersisted, Organization $persisted): void
    {
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willReturn($persisted);
        $this->prophesy(Presenter::class)->present($persisted)->shouldBeCalled()->willReturn(new stdClass());
    }
}
