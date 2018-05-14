<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RefreshUser;

use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserImpl;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserResponse;

use function Tests\Unit\Solean\Base\aUser;
use function Tests\Unit\Solean\Base\anOrganization;

class RefreshUserImplTest extends UseCaseTest
{
    public function target() : RefreshUserImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(UserGateway::class)->reveal(),
        ];
    }

    public function testCanCreateFindByUserName()
    {
        $this->assertInstanceOf($this->getTargetClassName(), $this->target());
    }

    public function testResponseIsReturnedWhenUserFound()
    {
        $request = aRequest()
            ->build();
        $entity = aUser()
            ->with('organization', anOrganization()->withCreatorData())
            ->build();

        $expectedResponse = aResponse()->build();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn($entity);

        /**
         * @var RefreshUserResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertEquals($expectedResponse->getId(), $response->getId());
        $this->assertEquals($expectedResponse->getUserName(), $response->getUserName());
        $this->assertEquals($expectedResponse->getPassword(), $response->getPassword());
        $this->assertEquals($expectedResponse->getRoles(), $response->getRoles());
        $this->assertEquals($expectedResponse->getOrganizationId(), $response->getOrganizationId());
    }

    public function testReturnNullWhenUserNotFound()
    {
        $request = aRequest()->build();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(null);

        $this->assertNull($this->target()->execute($request, $this->getMockedPresenter()));
    }
}

function aRequest()
{
    return new RefreshUserRequestBuilder();
}
function aResponse()
{
    return new RefreshUserResponseBuilder();
}
