<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Login;

use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\UseCase\Login\LoginImpl;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\UseCase\Login\LoginResponse;
use Solean\CleanProspecter\Exception\UseCase\BadCredentialException;

use function Tests\Unit\Solean\Base\aUser;

class LoginImplTest extends UseCaseTest
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
        $request = aRequest()->build();
        $entity  = aUser()->build();
        $expectedResponse = aResponse()->build();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn($entity);

        /**
         * @var LoginResponse $response
         */
        $response = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

        $this->assertInstanceOf(LoginResponse::class, $response);
        $this->assertEquals($response->getUserName(), $entity->getUserName());
        $this->assertEquals($response->getPassword(), $request->getPassword());
        $this->assertEquals($response->getRoles(), $entity->getRoles());
    }

    public function testBadCredentialExceptionIsThrownWhenPasswordHasTypo()
    {
        $request = aRequest()
            ->withTypo()
            ->build();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(aUser()->build());
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request, $this->getMockedPresenter());
    }

    public function testBadCredentialExceptionIsThrownWhenUserIsUnknown()
    {
        $request = aRequest()->build();

        $this->prophesy(UserGateway::class)->findOneBy(['userName' => $request->getLogin()])->shouldBeCalled()->willReturn(null);
        $this->expectExceptionObject(new BadCredentialException());

        $this->target()->execute($request, $this->getMockedPresenter());
    }
}

function aRequest()
{
    return new LoginRequestBuilder();
}
function aResponse()
{
    return new LoginResponseBuilder();
}
