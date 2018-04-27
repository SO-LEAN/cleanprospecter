<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;

use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Login\LoginImpl;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\UseCase\Login\LoginResponse;
use Solean\CleanProspecter\UseCase\Login\LoginPresenter;
use Tests\Unit\Solean\CleanProspecter\Factory\UserFactory;
use Solean\CleanProspecter\Exception\UseCase\BadCredentialException;

class LoginImplTest extends TestCase
{
    public function target() : LoginImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(UserGateway::class)->reveal(),
        ];
    }

    public function testCanCreateLogin()
    {
        $this->assertInstanceOf($this->getTargetClassName(), $this->target());
    }

    public function testResponseIsReturnedWhenPasswordIsCorrectBeforeEncoding()
    {
        $request = LoginRequestFactory::regular();
        $entity  = UserFactory::regular();
        $expectedResponse = LoginResponseFactory::regular();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn($entity);
        $this->prophesy(LoginPresenter::class)->present($expectedResponse)->shouldBeCalled()->willReturnArgument(0);
        /**
         * @var LoginResponse $response
         */
        $response = $this->target()->execute($request, $this->prophesy(LoginPresenter::class)->reveal());

        $this->assertInstanceOf(LoginResponse::class, $response);
        $this->assertEquals($response->getUserName(), $entity->getUserName());
        $this->assertEquals($response->getPassword(), $request->getPassword());
        $this->assertEquals($response->getRoles(), $entity->getRoles());
    }

    public function testBadCredentialExceptionIsThrownWhenPasswordHasTypo()
    {
        $request = LoginRequestFactory::typo();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(UserFactory::regular());
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request, $this->prophesy(LoginPresenter::class)->reveal());
    }

    public function testBadCredentialExceptionIsThrownWhenUserIsUnknown()
    {
        $request = LoginRequestFactory::typo();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(null);
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request, $this->prophesy(LoginPresenter::class)->reveal());
    }
}
