<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\GetOrganization;

use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationImpl;
use Solean\CleanProspecter\UseCase\GetOrganization\GetOrganizationRequest;
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
        $request = new GetOrganizationRequest(777);
        $persisted = anOrganization()
            ->withId()
            ->build();
        $expectedResponse = new GetOrganizationResponse(123, null, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, null, null, 'observ.', null, null, null, null, ['activeOrganizations' => 0]);

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnRegularWithOwner()
    {
        $request = new GetOrganizationRequest(777);
        $persisted = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();
        $expectedResponse = new GetOrganizationResponse(123, 777, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, null, null, 'observ.', null, null, null, null, ['activeOrganizations' => 0]);

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnRegularWithAddress()
    {
        $request = new GetOrganizationRequest(777);
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->with('geoPoint', aGeoPoint())
            ->build();
        $expectedResponse = new GetOrganizationResponse(123, null, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', '10 Downing Street', 'SW1A 2AA', 'London', 'EN', 7.7663456, 48.5554971, 'observ.', null, null, null, null, ['activeOrganizations' => 0]);

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnRegularWithLogo()
    {
        $request = new GetOrganizationRequest(777);
        $persisted = anOrganization()
            ->withId()
            ->with('logo', aFile()->withImageData())
            ->build();
        $expectedResponse = new GetOrganizationResponse(123, null, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, null, null, 'observ.', 'http://url.net/image.png', 'png', 2500, null, ['activeOrganizations' => 0]);

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnHold()
    {
        $request = new GetOrganizationRequest(777);
        $persisted = anOrganization()
            ->withId()
            ->with('holdBy', anOrganization()->withHoldingData())
            ->build();
        $expectedResponse = new GetOrganizationResponse(123, null, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, null, null, 'observ.', null, null, null, 456, ['activeOrganizations' => 0]);

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testExecuteOnFullFilled()
    {
        $request = new GetOrganizationRequest(777);
        $persisted = anOrganization()
            ->withId()
            ->with('address', anAddress())
            ->ownedBy(anOrganization()->withCreatorData())
            ->with('geoPoint', aGeoPoint())
            ->with('logo', aFile()->withImageData())
            ->with('holdBy', anOrganization()->withHoldingData())
            ->build();
        $expectedResponse = new GetOrganizationResponse(123, 777, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', '10 Downing Street', 'SW1A 2AA', 'London', 'EN', 7.7663456, 48.5554971, 'observ.', 'http://url.net/image.png', 'png', 2500, 456, ['activeOrganizations' => 0]);

        $this->mock($persisted);

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));
    }

    public function testThrowAnUseCaseNotFoundExceptionIfOrganizationNotFoundInGateway()
    {
        $request = new GetOrganizationRequest(777);
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
