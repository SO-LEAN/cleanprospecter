<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateOrganization;

use SplFileInfo;
use Prophecy\Argument;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Entity\GeoPoint;
use Solean\CleanProspecter\Gateway\GeoLocation;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationRequest;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationImpl;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationResponse;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aGeoPoint;
use function Tests\Unit\Solean\Base\aFile;

class UpdateOrganizationImplTest extends UseCaseTest
{
    public function target() : UpdateOrganizationImpl
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

    public function testProspectorCanUpdateOrganization()
    {
        $this->assertArraySubset(['ROLE_PROSPECTOR'], $this->target()->canBeExecutedBy());
    }

    /**
     * @dataProvider provideExecute
     */
    public function testExecute($expectedResponse, $request, $initial, $updated)
    {
        $this->mock($initial, $updated);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function provideExecute()
    {
        $req  = aRequest();
        $org  = anOrganization();
        $resp = aResponse();

        (yield 'no change' => [
            $resp->ownedByCreator()->build(),
            $req->build(),
            $org->withId()->ownedBy(anOrganization()->withCreatorData())->build(),
            $org->build(),
        ]);
        (yield 'full (with address) to empty (almost)' => [
            $resp->reset()->withId()->ownedByCreator()->named()->build(),
            $req->reset()->withId()->named()->build(),
            $org->reset()->withId()->withData()->ownedBy(anOrganization()->withCreatorData())->with('address', anAddress())->build(),
            $org->reset()->withId()->ownedBy(anOrganization()->withCreatorData())->named()->build(),
        ]);
    }

    public function testExecuteWithNewAddress()
    {
        $request = aRequest()->withNewAddress()->build();

        $orgBuilder  = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData());

        $initial = $orgBuilder
            ->withId()
            ->with('address', anAddress())->build();

        $updated = $orgBuilder
            ->with('address', anAddress()->withNewData())
            ->with('geoPoint', $geoPointBuilder = aGeoPoint())
            ->build();
        $expectedResponse = aResponse()->withNewAddress()->build();

        $this->mock($initial, $updated);
        $this->mockGeoLocation($geoPointBuilder->build());

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteWithNewAddressButNotFoundByGeoLocation()
    {
        $request = aRequest()
            ->withUnLocatableAddress()
            ->build();

        $orgBuilder  = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData());

        $initial = $orgBuilder
            ->withId()
            ->with('address', anAddress())
            ->with('geoPoint', aGeoPoint())
            ->build();

        $updated = $orgBuilder
            ->with('address', anAddress()->withUnLocatableAddress())
            ->with('geoPoint', null)
            ->build();

        $expectedResponse = aResponse()->withUnLocatableAddress()->build();

        $this->mock($initial, $updated);
        $this->mockGeoLocation(aGeoPoint()->notFound()->build(), false);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteEmptyToFull()
    {

        $request = aRequest()
            ->withNewData()
            ->withNewAddress()
            ->build();

        $orgBuilder  = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData());

        $initial = $orgBuilder
            ->reset()
            ->withId()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->build();

        $updated = $orgBuilder
            ->withNewData()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->with('address', anAddress()->withNewData())
            ->with('geoPoint', $geoPointBuilder = aGeoPoint())
            ->build();

        $expectedResponse = aResponse()->withNewData()->withNewAddress()->build();

        $this->mock($initial, $updated);
        $this->mockGeoLocation($geoPointBuilder->build());

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithLogo()
    {
        $organizationBuilder = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData());

        $get     = $organizationBuilder->build();
        $updated =  $organizationBuilder->withId()->with('logo', aFile()->withImageData())->build();

        $file = $this->mockFile($updated);

        $request = aRequest()
            ->withLogo($file)
            ->build();

        $expectedResponse = aResponse()
            ->ownedByCreator()
            ->withLogo()
            ->build();

        $this->mock($get, $updated);
        $this->mockStorage($file, $updated);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnHold()
    {
        $request = aRequest()
            ->hold()
            ->build();

        $organizationBuilder = anOrganization()->withId()->ownedBy(anOrganization()->withCreatorData());

        $get = $organizationBuilder->build();
        $updated =  $organizationBuilder->holdBy(anOrganization()->withHoldingData())->build();

        $expectedResponse = aResponse()
            ->ownedByCreator()
            ->hold()
            ->build();

        $this->mockOrganizationGateway($request);
        $this->mock($get, $updated);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse, $response);
    }

    public function testThrowAnUseCaseNotFoundExceptionIfHoldingNotFoundInGatewayDuringExecuteOnHold()
    {
        $request = aRequest()
            ->hold()
            ->build();

        $gatewayException = new Gateway\NotFoundException();
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn(anOrganization()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = aRequest()
            ->withId()
            ->build();

        $updated = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();

        $gatewayException = new Gateway\UniqueConstraintViolationException();
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn($updated);
        $this->prophesy(OrganizationGateway::class)->update($request->getId(), $updated)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\UniqueConstraintViolationException('Email already used', 412, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    public function testThrowUseCaseExceptionIfMissingCorporateNameAndEmail()
    {
        $request = aRequest()
            ->missingMandatoryData()
            ->build();

        $get = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();

        $this->expectExceptionObject(new UseCase\UseCaseException('At least one is mandatory : corporate name or email', 412));
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn($get);

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    private function mock(Organization $get, Organization $updated): void
    {
        $this->prophesy(OrganizationGateway::class)->get(123)->shouldBeCalled()->willReturn($get);
        $this->prophesy(OrganizationGateway::class)->update($updated->getId(), $updated)->shouldBeCalled()->willReturn($updated);
        $this->prophesy(UserNotifier::class)->addSuccess('Organization updated !')->shouldBeCalled();
    }

    private function mockOrganizationGateway(UpdateOrganizationRequest $request): void
    {
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willReturn(anOrganization()->withHoldingData()->build());
    }

    private function mockFile(Organization $updated): SplFileInfo
    {
        $this->prophesy(SplFileInfo::class)->getSize()->shouldBeCalled()->willReturn($updated->getLogo()->getSize());
        $this->prophesy(SplFileInfo::class)->getExtension()->shouldBeCalled()->willReturn($updated->getLogo()->getExtension());

        return $this->prophesy(SplFileInfo::class)->reveal();
    }

    private function mockStorage($file, Organization $updated): void
    {
        $this->prophesy(Storage::class)->add($file)->shouldBeCalled()->willReturn($updated->getLogo()->getUrl());
    }

    private function mockGeoLocation(GeoPoint $expectedPoint, $found = true): void
    {
        $this->prophesy(GeoLocation::class)->find(Argument::type('string'))->shouldBeCalled()->willReturn(new GeoLocation\GeoPointResponse('address', $expectedPoint->getLongitude(), $expectedPoint->getLatitude(), $found));
    }
}

function aRequest()
{
    return new UpdateOrganizationRequestBuilder();
}
function aResponse()
{
    return new UpdateOrganizationResponseBuilder();
}
