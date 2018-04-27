<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\RefreshUser;

use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Tests\Unit\Solean\CleanProspecter\Factory\UserFactory;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserImpl;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserResponse;
use Solean\CleanProspecter\UseCase\RefreshUser\RefreshUserPresenter;

class RefreshUserImplTest extends TestCase
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
        $request = RefreshUserRequestFactory::regular();
        $entity = UserFactory::regular();
        $expectedResponse = RefreshUserResponseFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn($entity);
        $this->prophesy(RefreshUserPresenter::class)->present($expectedResponse)->shouldBeCalled()->willReturnArgument(0);
        /**
         * @var RefreshUserResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(RefreshUserPresenter::class)->reveal());

        $this->assertInstanceOf(RefreshUserResponse::class, $response);
        $this->assertEquals($response->getId(), $entity->getId());
        $this->assertEquals($response->getUserName(), $entity->getUserName());
        $this->assertEquals($response->getPassword(), $entity->getPassword());
        $this->assertEquals($response->getRoles(), $entity->getRoles());
        $this->assertEquals($response->getOrganizationId(), $entity->getOrganization()->getId());
    }

    public function testReturnNullWhenUserNotFound()
    {
        $request = RefreshUserRequestFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(null);

        $this->assertNull($this->target()->execute($request, $this->prophesy(RefreshUserPresenter::class)->reveal()));
    }
}
