<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateOrganization;

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
            $req->ownedByCreator()->build(),
            $org->withId()->ownedBy(anOrganization()->withCreatorData())->build(),
            $org->build(),
        ]);
        (yield 'full (with address) to empty (almost)' => [
            $resp->reset()->withId()->ownedByCreator()->named()->build(),
            $req->reset()->withId()->ownedByCreator()->named()->build(),
            $org->reset()->withId()->withData()->ownedBy(anOrganization()->withCreatorData())->with('address', anAddress())->build(),
            $org->reset()->withId()->ownedBy(anOrganization()->withCreatorData())->named()->build(),
        ]);
        (yield 'new address' => [
            $resp->reset()->withId()->ownedByCreator()->named()->withNewAddress()->build(),
            $req->reset()->withId()->ownedByCreator()->named()->withNewAddress()->build(),
            $org->reset()->withId()->named()->ownedBy(anOrganization()->withCreatorData())->with('address', anAddress())->build(),
            $org->reset()->withId()->named()->ownedBy(anOrganization()->withCreatorData())->with('address', anAddress()->withNewData())->build(),
        ]);

        (yield 'empty to full' => [
            $resp->reset()->withId()->ownedByCreator()->withData()->withNewAddress()->build(),
            $req->reset()->withId()->ownedByCreator()->withData()->withNewAddress()->build(),
            $org->reset()->withId()->ownedBy(anOrganization()->withCreatorData())->named()->build(),
            $org->reset()->withId()->ownedBy(anOrganization()->withCreatorData())->withData()->ownedBy(anOrganization()->withCreatorData())->with('address', anAddress()->withNewData())->build(),
        ]);
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
            ->ownedByCreator()
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
            ->ownedByCreator()
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
            ->ownedByCreator()
            ->hold()
            ->build();

        $gatewayException = new Gateway\NotFoundException();
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn(anOrganization()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getOwnedBy())->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = anUpdateOrganizationRequest()
            ->ownedByCreator()
            ->withId()
            ->build();

        $updated = anOrganization()
            ->withId()
            ->ownedBy(anOrganization()->withCreatorData())
            ->build();

        $gatewayException = new Gateway\UniqueConstraintViolationException();
        $this->prophesy(OrganizationGateway::class)->get($request->getId())->shouldBeCalled()->willReturn(anOrganization()->withId()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getOwnedBy())->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
        $this->prophesy(OrganizationGateway::class)->update($request->getId(), $updated)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\UniqueConstraintViolationException('Email already used', 412, $gatewayException));

        $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());
    }

    public function testThrowUseCaseExceptionIfMissingCorporateNameAndEmail()
    {
        $request = anUpdateOrganizationRequest()
            ->ownedByCreator()
            ->missingMandatoryData()
            ->build();

        $this->expectExceptionObject(new UseCase\UseCaseException('At least one is mandatory : corporate name or email', 412));

        $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());
    }

    public function testThrowUseCaseExceptionIfMissingOwner()
    {
        $request = anUpdateOrganizationRequest()
            ->build();

        $this->expectExceptionObject(new UseCase\UseCaseException('Owner is missing', 412));

        $this->target()->execute($request, $this->prophesy(UpdateOrganizationPresenter::class)->reveal());
    }

    private function mock(Organization $get, Organization $updated, UpdateOrganizationResponse $expectedResponse): void
    {
        $this->prophesy(OrganizationGateway::class)->get(123)->shouldBeCalled()->willReturn($get);
        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
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
}

function anUpdateOrganizationRequest()
{
    return new UpdateOrganizationRequestBuilder();
}
function anUpdateOrganizationResponse()
{
    return new UpdateOrganizationResponseBuilder();
}
