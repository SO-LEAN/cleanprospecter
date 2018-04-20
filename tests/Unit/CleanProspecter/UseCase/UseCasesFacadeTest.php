<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase;

use BadFunctionCallException;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\UseCase\UseCasesFacade;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubUseCaseImpl;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubUseCaseRequest;

class UseCasesFacadeTest extends TestCase
{
    public function target() : UseCasesFacade
    {
        return parent::target();
    }

    public function initialize(): void
    {
        $this->target()->addUseCase(new StubUseCaseImpl());
    }

    public function testUseCaseCanBeAdded() : void
    {
        $this->assertTrue($this->target()->hasUseCase('stubUseCase'));
    }

    public function testMethodFromUseCaseIsCorrectlyCalled() : void
    {
        $response = $this->target()->stubUseCase(new StubUseCaseRequest(), $this->prophesy(Presenter::class)->reveal());
        $this->assertEquals('executed', $response->action);
    }

    public function testMethodCalledOnUnknownUseCaseThrowsABadFunctionCallException() : void
    {
        $this->expectException(BadFunctionCallException::class);
        $this->expectExceptionMessage('Solean\CleanProspecter\UseCase\UseCasesFacade::unknownUseCase()');

        $this->target()->unknownUseCase(new StubUseCaseRequest(), $this->prophesy(Presenter::class)->reveal());
    }
}
