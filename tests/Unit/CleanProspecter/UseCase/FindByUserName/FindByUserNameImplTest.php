<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindByUserName;

use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Tests\Unit\Solean\CleanProspecter\Factory\UserFactory;
use Solean\CleanProspecter\UseCase\FindByUserName\FindByUserNameImpl;
use Solean\CleanProspecter\UseCase\FindByUserName\FindByUserNameResponse;

class FindByUserNameImplTest extends TestCase
{
    public function target() : FindByUserNameImpl
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
        $request = FindByUserNameRequestFactory::regular();
        $entity = UserFactory::regular();
        $expectedResponse = FindByUserNameResponseFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn($entity);
        $this->prophesy(Presenter::class)->present($expectedResponse)->shouldBeCalled()->willReturnArgument(0);
        /**
         * @var FindByUserNameResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(Presenter::class)->reveal());

        $this->assertInstanceOf(FindByUserNameResponse::class, $response);
        $this->assertEquals($response->getId(), $entity->getId());
        $this->assertEquals($response->getUserName(), $entity->getUserName());
        $this->assertEquals($response->getPassword(), $entity->getPassword());
        $this->assertEquals($response->getRoles(), $entity->getRoles());
        $this->assertEquals($response->getOrganizationId(), $entity->getOrganization()->getId());
    }

    public function testReturnNullWhenUserNotFound()
    {
        $request = FindByUserNameRequestFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(null);

        $this->assertNull($this->target()->execute($request, $this->prophesy(Presenter::class)->reveal()));
    }
}
