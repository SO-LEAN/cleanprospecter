<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use SplFileInfo;
use Prophecy\Argument;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Entity\GeoPoint;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Gateway\GeoLocation;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationImpl;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationRequest;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationResponse;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\aGeoPoint;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aFile;

class CreateOrganizationImplTest extends UseCaseTest
{
    public function target() : CreateOrganizationImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(OrganizationGateway::class)->reveal(),
            $this->prophesy(Storage::class)->reveal(),
            $this->prophesy(UserNotifier::class)->reveal(),
            $this->prophesy(GeoLocation::class)->reveal(),
        ];
    }

    public function testProspectorCanCreateOrganization()
    {
        $this->assertArraySubset(['ROLE_PROSPECTOR'], $this->target()->canBeExecutedBy());
    }

    public function testExecuteOnRegularWithCreator()
    {
        $request = aRequest()
            ->ownedByCreator()
            ->build();

        $organizationBuilder = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData());

        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->withId()->build();

        $expectedResponse = aResponse()->build();

        $this->mock($notPersisted, $persisted);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
        $this->assertEquals(1, $persisted->getOwnedBy()->getStat('activeOrganizations'));
    }

    public function testExecuteOnRegularWithAddress()
    {
        $request = aRequest()
            ->ownedByCreator()
            ->withAddress()
            ->build();

        $organizationBuilder = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData())
            ->with('address', anAddress())
            ->with('geoPoint', $geoPoint = aGeoPoint())
        ;

        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->withId()->build();

        $expectedResponse = aResponse()
            ->withRegularAddress()
            ->build();

        $this->mock($notPersisted, $persisted);
        $this->mockGeoLocation($geoPoint->build());

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithLogo()
    {
        $organizationBuilder = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData())
            ->with('logo', aFile()->withImageData());

        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->withId()->build();

        $file = $this->mockFile($notPersisted);

        $request = aRequest()
            ->ownedByCreator()
            ->withLogo($file)
            ->build();

        $expectedResponse = aResponse()
            ->withLogo()
            ->build();

        $this->mock($notPersisted, $persisted);
        $this->mockStorage($file, $notPersisted);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnHold()
    {
        $request = aRequest()
            ->ownedByCreator()
            ->hold()
            ->build();

        $organizationBuilder = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData())
            ->with('holdBy', anOrganization()->withHoldingData());

        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->withId()->build();

        $expectedResponse = aResponse()
            ->hold()
            ->build();

        $this->mockGetHolding($request);
        $this->mock($notPersisted, $persisted);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testThrowAnUseCaseNotFoundExceptionIfHoldingNotFoundInGatewayDuringExecuteOnHold()
    {
        $request = aRequest()
            ->ownedByCreator()
            ->hold()
            ->build();

        $gatewayException = new Gateway\NotFoundException();

        $this->prophesy(OrganizationGateway::class)->get($request->getOwnedBy())->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = aRequest()
            ->ownedByCreator()
            ->build();

        $notPersisted = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();

        $gatewayException = new Gateway\UniqueConstraintViolationException();

        $this->prophesy(OrganizationGateway::class)->get($request->getOwnedBy())->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\UniqueConstraintViolationException('Email already used', 412, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    public function testThrowUseCaseExceptionIfMissingCorporateNameAndEmail()
    {
        $request = aRequest()
            ->ownedByCreator()
            ->missingMandatoryData()
            ->build();

        $this->mockGetCreator();
        $this->expectExceptionObject(new UseCase\UseCaseException('At least one is mandatory : corporate name or email', 412));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    public function testThrowUseCaseExceptionIfMissingOwner()
    {
        $request = aRequest()
            ->build();

        $this->expectExceptionObject(new UseCase\UseCaseException('Owner is missing', 412));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    private function mock(Organization $notPersisted, Organization $persisted): void
    {
        $this->mockGetCreator();
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willReturn($persisted);
        $this->prophesy(UserNotifier::class)->addSuccess('Organization created !')->shouldBeCalled();
    }

    private function mockGetCreator(): void
    {
        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
    }

    private function mockFile(Organization $notPersisted): SplFileInfo
    {
        $this->prophesy(SplFileInfo::class)->getSize()->shouldBeCalled()->willReturn($notPersisted->getLogo()->getSize());
        $this->prophesy(SplFileInfo::class)->getExtension()->shouldBeCalled()->willReturn($notPersisted->getLogo()->getExtension());

        return $this->prophesy(SplFileInfo::class)->reveal();
    }

    private function mockStorage($file, Organization $notPersisted): void
    {
        $this->prophesy(Storage::class)->add($file)->shouldBeCalled()->willReturn($notPersisted->getLogo()->getUrl());
    }

    private function mockGetHolding(CreateOrganizationRequest $request): void
    {
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willReturn(anOrganization()->withHoldingData()->build());
    }

    private function mockGeoLocation(GeoPoint $expectedPoint): void
    {
        $this->prophesy(GeoLocation::class)->find(Argument::type('string'))->shouldBeCalled()->willReturn(new GeoLocation\GeoPointResponse('address', $expectedPoint->getLongitude(), $expectedPoint->getLatitude(), true));
    }
}

function aRequest()
{
    return new CreateOrganizationRequestBuilder();
}
function aResponse()
{
    return new CreateOrganizationResponseBuilder();
}
