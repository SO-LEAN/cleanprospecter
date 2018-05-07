<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateOrganization;

use Prophecy\Argument;
use Solean\CleanProspecter\Entity\GeoPoint;
use Solean\CleanProspecter\Gateway\GeoLocation;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationRequest;
use SplFileInfo;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationImpl;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationResponse;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationPresenter;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aGeoPoint;
use function Tests\Unit\Solean\Base\aFile;

class UpdateOrganizationImplTest extends TestCase
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
        $this->mock($initial, $updated, $expectedResponse);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function provideExecute()
    {
        $req  = anUpdateOrganizationRequest();
        $org  = anOrganization();
        $resp = anUpdateOrganizationResponse();

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
        $request = anUpdateOrganizationRequest()->withNewAddress()->build();

        $orgBuilder  = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData());

        $initial = $orgBuilder
            ->withId()
            ->with('address', anAddress())->build();

        $updated = $orgBuilder
            ->with('address', anAddress()->withNewData())
            ->with('geoPoint', $geoPointBuilder = aGeoPoint())
            ->build();
        $expectedResponse = anUpdateOrganizationResponse()->withNewAddress()->build();

        $this->mock($initial, $updated, $expectedResponse);
        $this->mockGeoLocation($geoPointBuilder->build());

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteWithNewAddressButNotFoundByGeoLocation()
    {
        $request = anUpdateOrganizationRequest()
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

        $expectedResponse = anUpdateOrganizationResponse()->withUnLocatableAddress()->build();

        $this->mock($initial, $updated, $expectedResponse);
        $this->mockGeoLocation(aGeoPoint()->notFound()->build(), false);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteEmptyToFull()
    {

        $request = anUpdateOrganizationRequest()
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

        $expectedResponse = anUpdateOrganizationResponse()->withNewData()->withNewAddress()->build();

        $this->mock($initial, $updated, $expectedResponse);
        $this->mockGeoLocation($geoPointBuilder->build());

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());

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

        $request = anUpdateOrganizationRequest()
            ->withLogo($file)
            ->build();

        $expectedResponse = anUpdateOrganizationResponse()
            ->ownedByCreator()
            ->withLogo()
            ->build();

        $this->mock($get, $updated, $expectedResponse);
        $this->mockStorage($file, $updated);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testExecuteOnHold()
    {
        $request = anUpdateOrganizationRequest()
            ->hold()
            ->build();

        $organizationBuilder = anOrganization()->withId()->ownedBy(anOrganization()->withCreatorData());

        $get = $organizationBuilder->build();
        $updated =  $organizationBuilder->holdBy(anOrganization()->withHoldingData())->build();

        $expectedResponse = anUpdateOrganizationResponse()
            ->ownedByCreator()
            ->hold()
            ->build();

        $this->mockOrganizationGateway($request);
        $this->mock($get, $updated, $expectedResponse);

        /**
         * @var UpdateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());

        $this->assertEquals($expectedResponse, $response);
    }

    public function testThrowAnUseCaseNotFoundExceptionIfHoldingNotFoundInGatewayDuringExecuteOnHold()
    {
        $request = anUpdateOrganizationRequest()
            ->hold()
            ->build();

        $gatewayException = new Gateway\NotFoundException();
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn(anOrganization()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = anUpdateOrganizationRequest()
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

        $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());
    }

    public function testThrowUseCaseExceptionIfMissingCorporateNameAndEmail()
    {
        $request = anUpdateOrganizationRequest()
            ->missingMandatoryData()
            ->build();

        $get = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();

        $this->expectExceptionObject(new UseCase\UseCaseException('At least one is mandatory : corporate name or email', 412));
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn($get);

        $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());
    }

    private function mock(Organization $get, Organization $updated, UpdateOrganizationResponse $expectedResponse): void
    {
        $this->prophesy(OrganizationGateway::class)->get(123)->shouldBeCalled()->willReturn($get);
        $this->prophesy(OrganizationGateway::class)->update($updated->getId(), $updated)->shouldBeCalled()->willReturn($updated);
        $this->prophesy(UserNotifier::class)->addSuccess('Organization updated !')->shouldBeCalled();
        $this->prophesy(UpdateOrganizationPresenter::class)->present($expectedResponse)->shouldBeCalled()->willReturnArgument(0);
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

function anUpdateOrganizationRequest()
{
    return new UpdateOrganizationRequestBuilder();
}
function anUpdateOrganizationResponse()
{
    return new UpdateOrganizationResponseBuilder();
}
