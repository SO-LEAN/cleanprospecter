<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;

use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Login\LoginImpl;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\UseCase\Login\LoginResponse;
use Solean\CleanProspecter\UseCase\Login\LoginPresenter;
use Solean\CleanProspecter\Exception\UseCase\BadCredentialException;

use function Tests\Unit\Solean\Base\aUser;

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
        $request = aLoginRequest()->build();
        $entity  = aUser()->build();
        $expectedResponse = aLoginResponse()->build();

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
        $request = aLoginRequest()
            ->withTypo()
            ->build();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(aUser()->build());
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request, $this->prophesy(LoginPresenter::class)->reveal());
    }

    public function testBadCredentialExceptionIsThrownWhenUserIsUnknown()
    {
        $request = aLoginRequest()->build();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(null);
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request, $this->prophesy(LoginPresenter::class)->reveal());
    }
}

function aLoginRequest()
{
    return new LoginRequestBuilder();
}
function aLoginResponse()
{
    return new LoginResponseBuilder();
}
