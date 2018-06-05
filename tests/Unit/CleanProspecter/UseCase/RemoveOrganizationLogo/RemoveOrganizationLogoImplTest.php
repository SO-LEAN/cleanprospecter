<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RemoveOrganizationLogo;

use Solean\CleanProspecter\Exception\UseCase\UseCaseException;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\RemoveOrganizationLogo\RemoveOrganizationLogoImpl;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\aFile;

class RemoveOrganizationLogoImplTest extends UseCaseTest
{
    public function target() : RemoveOrganizationLogoImpl
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

    public function testProspectorCanRemoveOrganizationLogo()
    {
        $this->assertArraySubset(['ROLE_PROSPECTOR'], $this->target()->canBeExecutedBy());
    }

    public function testExecute()
    {
        $request = aRequest()->build();
        $expectedResponse = aResponse()->build();

        $organization = anOrganization()
            ->withId()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->with('logo', aFile()->withImageData())
            ->build();

        $ownerOrganizationId = $organization->getOwnedBy()->getId();

        $this->prophesy(UseCaseConsumer::class)
            ->getOrganizationId()
            ->shouldBeCalled()
            ->willReturn($ownerOrganizationId);

        $this->prophesy(OrganizationGateway::class)
           ->get($organization->getId())
           ->shouldBeCalled()
           ->willReturn($organization);

        $this->prophesy(UserNotifier::class)
            ->addSuccess('Organization logo was removed !')
            ->shouldBeCalled();

        $this->target()->execute($request, $this->getMockedPresenter($expectedResponse), $this->prophesy(UseCaseConsumer::class)->reveal());
    }

    public function testAnExceptionThrownWhenTryToRemoveLogoOnNonBelongToOrganization()
    {
        $request = aRequest()->build();

        $organization = anOrganization()
            ->withId()
            ->with('ownedBy', anOrganization()->withCreatorData())
            ->with('logo', aFile()->withImageData())
            ->build();

        $this->prophesy(UseCaseConsumer::class)
            ->getOrganizationId()
            ->shouldBeCalled()
            ->willReturn(888);

        $this->prophesy(OrganizationGateway::class)
            ->get($organization->getId())
            ->shouldBeCalled()
            ->willReturn($organization);

        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('Organization "Prospector Organization Limited Company" does not belong to your organization.');

        $this->target()->execute($request, $this->getMockedPresenter(), $this->prophesy(UseCaseConsumer::class)->reveal());
    }
}

function aRequest()
{
    return new RemoveOrganizationLogoRequestBuilder();
}
function aResponse()
{
    return new RemoveOrganizationLogoResponseBuilder();
}
