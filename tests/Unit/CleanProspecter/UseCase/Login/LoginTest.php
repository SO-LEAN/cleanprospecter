<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;

use Solean\CleanProspecter\Exception\UseCase\BadCredentialException;
use stdClass;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\UseCase\Login\Login;
use Solean\CleanProspecter\Gateway\Database\UserGateway;
use Tests\Unit\Solean\CleanProspecter\Factory\UserFactory;
use Solean\CleanProspecter\Exception\UseCase\UnauthorizedException;


class LoginTest extends TestCase
{
    public function target() : Login
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

    public function testCanCreateLogin()
    {
        $this->assertInstanceOf($this->getTargetClassName(), $this->target());
    }

    public function testThePresentationIsReturnedWhenPasswordIsCorrectBeforeEncoding()
    {
        $request = LoginRequestFactory::regular();
        $expectedResponse = LoginResponseFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(UserFactory::regular());
        $this->prophesy(Presenter::class)->present($expectedResponse)->shouldBeCalled()->willReturn(new stdClass());

        $this->assertEquals(new stdClass(), $this->target()->execute($request));

    }

    public function testBadCredentialExceptionIsThrownWhenPasswordHasTypo()
    {
        $request = LoginRequestFactory::typo();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(UserFactory::regular());
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request);
    }

    public function testBadCredentialExceptionIsThrownWhenUserIsUnknown()
    {
        $request = LoginRequestFactory::typo();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(null);
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request);
    }

}