<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindByUserName;

use stdClass;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\Gateway\Database\UserGateway;
use Tests\Unit\Solean\CleanProspecter\Factory\UserFactory;
use Solean\CleanProspecter\UseCase\FindByUserName\FindByUserName;

class FindByUserNameTest extends TestCase
{
    public function target() : FindByUserName
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(UserGateway::class)->reveal(),
            $this->prophesy(Presenter::class)->reveal(),
        ];
    }

    public function testCanCreateFindByUserName()
    {
        $this->assertInstanceOf($this->getTargetClassName(), $this->target());
    }

    public function testThePresentationIsReturnedWhenUserFound()
    {
        $request = FindByUserNameRequestFactory::regular();
        $expectedResponse = FindByUserNameResponseFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['login' => $request->getLogin()])->shouldBeCalled()->willReturn(UserFactory::regular());
        $this->prophesy(Presenter::class)->present($expectedResponse)->shouldBeCalled()->willReturn(new stdClass());

        $this->assertEquals(new stdClass(), $this->target()->execute($request));

    }

    public function testReturnNullWhenUserNotFound()
    {
        $request = FindByUserNameRequestFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['login' => $request->getLogin()])->shouldBeCalled()->willReturn(null);

        $this->assertNull($this->target()->execute($request));
    }

}