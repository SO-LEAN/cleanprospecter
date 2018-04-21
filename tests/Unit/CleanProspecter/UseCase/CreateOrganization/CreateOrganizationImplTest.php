<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

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

    public function testExecuteOnRegular()
    {
        $request = CreateOrganizationRequestFactory::regular();
        $notPersisted = OrganizationFactory::notPersistedRegular();
        $persisted = OrganizationFactory::regular();

        $this->mock($notPersisted, $persisted);

        $this->target()->execute($request, $this->prophesy(Presenter::class)->reveal());
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

    /**
     * @param Organization $notPersisted
     * @param Organization $persisted
     */
    private function mock(Organization $notPersisted, Organization $persisted): void
    {
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willReturn($persisted);
        $this->prophesy(Presenter::class)->present($persisted)->shouldBeCalled()->willReturn(new stdClass());
    }

}
