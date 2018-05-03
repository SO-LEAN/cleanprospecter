<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\CreateOrganization;

use \SplFileInfo;
use Solean\CleanProspecter\Gateway\Storage;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\UseCase;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationImpl;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationRequest;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationResponse;
use Solean\CleanProspecter\UseCase\CreateOrganization\CreateOrganizationPresenter;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aFile;

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
            $this->prophesy(Storage::class)->reveal(),
            $this->prophesy(UserNotifier::class)->reveal(),
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

    public function testExecuteOnRegularWithCreator()
    {
        $request = aCreateOrganizationRequest()
            ->ownedByCreator()
            ->build();

        $organizationBuilder = anOrganization()
            ->with('ownedBy', anOrganization()->withCreatorData());
        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->persistedAsRegular()->build();

        $expectedResponse = aCreateOrganizationResponse()->build();

        $this->mock($notPersisted, $persisted, $expectedResponse);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());

        $this->assertOwnedByEquals($expectedResponse, $response);
        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithAddress()
    {
        $request = aCreateOrganizationRequest()
            ->ownedByCreator()
            ->withAddress()
            ->build();

        $organizationBuilder = anOrganization()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->with('address', anAddress());
        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->persistedAsRegular()->build();

        $expectedResponse = aCreateOrganizationResponse()
            ->withRegularAddress()
            ->build();

        $this->mock($notPersisted, $persisted, $expectedResponse);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());

        $this->assertOwnedByEquals($expectedResponse, $response);
        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertAddressEquals($expectedResponse, $response);
    }

    public function testExecuteOnRegularWithLogo()
    {
        $organizationBuilder = anOrganization()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->with('logo', aFile()->withImageData());

        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->persistedAsRegular()->build();

        $file = $this->mockFile($notPersisted);

        $request = aCreateOrganizationRequest()
            ->ownedByCreator()
            ->withLogo($file)
            ->build();

        $expectedResponse = aCreateOrganizationResponse()
            ->withLogo()
            ->build();

        $this->mock($notPersisted, $persisted, $expectedResponse);
        $this->mockStorage($file, $notPersisted);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());

        $this->assertOwnedByEquals($expectedResponse, $response);
        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertLogoEquals($expectedResponse, $response);
    }

    public function testExecuteOnHold()
    {
        $request = aCreateOrganizationRequest()
            ->ownedByCreator()
            ->hold()
            ->build();

        $organizationBuilder = anOrganization()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->with('holdBy', anOrganization()->withHoldingData());

        $notPersisted = $organizationBuilder->build();
        $persisted =  $organizationBuilder->persistedAsRegular()->build();

        $expectedResponse = aCreateOrganizationResponse()
            ->hold()
            ->build();

        $this->mockOrganizationGateway($request);
        $this->mock($notPersisted, $persisted, $expectedResponse);

        /**
         * @var CreateOrganizationResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());

        $this->assertOwnedByEquals($expectedResponse, $response);
        $this->assertResponseEquals($expectedResponse, $response);
        $this->assertHoldByEquals($expectedResponse, $response);
    }

    public function testThrowAnUseCaseNotFoundExceptionIfHoldingNotFoundInGatewayDuringExecuteOnHold()
    {
        $request = aCreateOrganizationRequest()
            ->ownedByCreator()
            ->hold()
            ->build();

        $gatewayException = new Gateway\NotFoundException();

        $this->prophesy(OrganizationGateway::class)->get($request->getOwnedBy())->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\NotFoundException(sprintf('Holding with #ID %d not found', $request->getHoldBy()), 404, $gatewayException));

        $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());
    }

    public function testThrowAnUseCaseUniqueConstraintViolationExceptionIfGatewayThrowOne()
    {
        $request = aCreateOrganizationRequest()
            ->ownedByCreator()
            ->build();

        $notPersisted = anOrganization()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->build();

        $gatewayException = new Gateway\UniqueConstraintViolationException();

        $this->prophesy(OrganizationGateway::class)->get($request->getOwnedBy())->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willThrow($gatewayException);
        $this->expectExceptionObject(new UseCase\UniqueConstraintViolationException('Email already used', 412, $gatewayException));

        $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());
    }

    public function testThrowUseCaseExceptionIfMissingCorporateNameAndEmail()
    {
        $request = aCreateOrganizationRequest()
            ->ownedByCreator()
            ->missingMandatoryData()
            ->build();

        $this->expectExceptionObject(new UseCase\UseCaseException('At least one is mandatory : corporate name or email', 412));

        $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());
    }

    public function testThrowUseCaseExceptionIfMissingOwner()
    {
        $request = aCreateOrganizationRequest()
            ->build();

        $this->expectExceptionObject(new UseCase\UseCaseException('Owner is missing', 412));

        $this->target()->execute($request, $this->prophesy(CreateOrganizationPresenter::class)->reveal());
    }

    private function mock(Organization $notPersisted, Organization $persisted, CreateOrganizationResponse $expectedResponse): void
    {
        $this->prophesy(OrganizationGateway::class)->get(777)->shouldBeCalled()->willReturn(anOrganization()->withCreatorData()->build());
        $this->prophesy(OrganizationGateway::class)->create($notPersisted)->shouldBeCalled()->willReturn($persisted);
        $this->prophesy(UserNotifier::class)->addSuccess('Organization created !')->shouldBeCalled();
        $this->prophesy(CreateOrganizationPresenter::class)->present($expectedResponse)->shouldBeCalled()->willReturnArgument(0);
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

    private function mockOrganizationGateway(CreateOrganizationRequest $request): void
    {
        $this->prophesy(OrganizationGateway::class)->get($request->getHoldBy())->shouldBeCalled()->willReturn(anOrganization()->withHoldingData()->build());
    }

    private function assertResponseEquals(CreateOrganizationResponse $expectedResponse, CreateOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getId(), $response->getId());
        $this->assertEquals($expectedResponse->getPhoneNumber(), $response->getPhoneNumber());
        $this->assertEquals($expectedResponse->getEmail(), $response->getEmail());
        $this->assertEquals($expectedResponse->getLanguage(), $response->getLanguage());
        $this->assertEquals($expectedResponse->getCorporateName(), $response->getCorporateName());
        $this->assertEquals($expectedResponse->getForm(), $response->getForm());
        $this->assertEquals($expectedResponse->getObservations(), $response->getObservations());
        $this->assertEquals($expectedResponse->getOwnedBy(), $response->getOwnedBy());
    }

    private function assertAddressEquals(CreateOrganizationResponse $expectedResponse, CreateOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getStreet(), $response->getStreet());
        $this->assertEquals($expectedResponse->getPostalCode(), $response->getPostalCode());
        $this->assertEquals($expectedResponse->getCity(), $response->getCity());
        $this->assertEquals($expectedResponse->getCountry(), $response->getCountry());
    }

    private function assertLogoEquals(CreateOrganizationResponse $expectedResponse, CreateOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getLogoUrl(), $response->getLogoUrl());
        $this->assertEquals($expectedResponse->getLogoExtension(), $response->getLogoExtension());
        $this->assertEquals($expectedResponse->getLogoSize(), $response->getLogoSize());
    }

    private function assertOwnedByEquals(CreateOrganizationResponse $expectedResponse, CreateOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getOwnedBy(), $response->getOwnedBy());
    }

    private function assertHoldByEquals(CreateOrganizationResponse $expectedResponse, CreateOrganizationResponse $response): void
    {
        $this->assertEquals($expectedResponse->getHoldBy(), $response->getHoldBy());
    }
}

function aCreateOrganizationRequest()
{
    return new CreateOrganizationRequestBuilder();
}
function aCreateOrganizationResponse()
{
    return new CreateOrganizationResponseBuilder();
}
