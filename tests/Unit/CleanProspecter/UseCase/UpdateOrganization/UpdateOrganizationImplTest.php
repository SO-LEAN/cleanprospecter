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
        $org  = anOrganization();

        (yield 'no change' => [
            new UpdateOrganizationResponse(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, null, null, 'observ.', null, null, null, null),
            new UpdateOrganizationRequest(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, 'observ.', null, null),
            $org->withId()->ownedBy(anOrganization()->withCreatorData())->build(),
            $org->build(),
        ]);
        (yield 'full (with address) to empty (almost)' => [
            new UpdateOrganizationResponse(123, null, null, null, 'Organization', null, null, null, null, null, null, null, null, null, null, null, null, null),
            new UpdateOrganizationRequest(123, null, null, null, 'Organization', null, null, null, null, null, null, null, null, null),
            $org->reset()->withId()->withData()->ownedBy(anOrganization()->withCreatorData())->with('address', anAddress())->build(),
            $org->reset()->withId()->ownedBy(anOrganization()->withCreatorData())->named()->build(),
        ]);
    }

    public function testExecuteWithNewAddress()
    {
        $request = new UpdateOrganizationRequest(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', '20 avenue du Neuhof', '67100', 'Strasbourg', 'FR', 'observ.', null, null);

        $orgBuilder  = anOrganization()
            ->ownedBy(anOrganization()->withCreatorData());

        $initial = $orgBuilder
            ->withId()
            ->with('address', anAddress())->build();

        $updated = $orgBuilder
            ->with('address', anAddress()->withNewData())
            ->with('geoPoint', $geoPointBuilder = aGeoPoint())
            ->build();
        $expectedResponse = new UpdateOrganizationResponse(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', '20 avenue du Neuhof', '67100', 'Strasbourg', 'FR', 7.7663456, 48.5554971, 'observ.', null, null, null, null);

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
        $request = new UpdateOrganizationRequest(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', '20 avenue du Not found', '67100', 'Not found', 'FR', 'observ.', null, null);

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

        $expectedResponse = new UpdateOrganizationResponse(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', '20 avenue du Not found', '67100', 'Not found', 'FR', null, null, 'observ.', null, null, null, null);

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
        $request = new UpdateOrganizationRequest(123, '111111111', 'org@new-organization.com', 'BE', 'New Organization', 'SARL', 'Direct', '20 avenue du Neuhof', '67100', 'Strasbourg', 'FR', 'new observ.', null, null);

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

        $expectedResponse = new UpdateOrganizationResponse(123, '111111111', 'org@new-organization.com', 'BE', 'New Organization', 'SARL', 'Direct', '20 avenue du Neuhof', '67100', 'Strasbourg', 'FR', 7.7663456, 48.5554971, 'new observ.', null, null, null, null);

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

        $request = new UpdateOrganizationRequest(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, 'observ.', $file, null);

        $expectedResponse = new UpdateOrganizationResponse(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, null, null, 'observ.', 'http://url.net/image.png', 'png', 2500, null);

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
        $request = new UpdateOrganizationRequest(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, 'observ.', null, 456);

        $organizationBuilder = anOrganization()->withId()->ownedBy(anOrganization()->withCreatorData());

        $get = $organizationBuilder->build();
        $updated =  $organizationBuilder->holdBy(anOrganization()->withHoldingData())->build();

        $expectedResponse = new UpdateOrganizationResponse(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, null, null, 'observ.', null, null, null, 456);

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
        $request = new UpdateOrganizationRequest(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, 'observ.', null, 456);

        $gatewayException = new Gateway\NotFoundException();
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn(anOrganization()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = new UpdateOrganizationRequest(123, '03777666888', 'org@organization.com', 'EN', 'Organization', 'Limited Company', 'Consulting', null, null, null, null, 'observ.', null, null);

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
        $request = new UpdateOrganizationRequest(123, '03777666888', null, 'EN', null, 'Limited Company', 'Consulting', null, null, null, null, 'observ.', null, null);

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
