<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase;

use BadFunctionCallException;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\UseCasesFacade;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubUseCase;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubUseCaseRequest;

class UseCasesFacadeTest extends TestCase
{
    public function target() : UseCasesFacade
    {
        return parent::target();
    }

    public function initialize(): void
    {
       $this->target()->addUseCase(new StubUseCase());
    }

    public function testUseCaseCanBeAdded() : void
    {
        $this->assertTrue($this->target()->hasUseCase('stubUseCase'));
    }

    public function testMethodFromUseCaseIsCorrectlyCalled() : void
    {
        $this->assertEquals('executed', $this->target()->stubUseCase(new StubUseCaseRequest()));
    }

    public function testMethodCalledOnUnknownUseCaseThrowsABadFunctionCallException() : void
    {
        $this->expectException(BadFunctionCallException::class);
        $this->expectExceptionMessage('Solean\CleanProspecter\UseCase\UseCasesFacade::unknownUseCase()');

        $this->assertEquals('executed', $this->target()->unknownUseCase(new StubUseCaseRequest()));
    }

}