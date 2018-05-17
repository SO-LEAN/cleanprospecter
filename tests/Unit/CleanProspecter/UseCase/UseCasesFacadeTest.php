<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase;

use BadFunctionCallException;
use Solean\CleanProspecter\Exception\UseCase\UnauthorizedException;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\UseCase\UseCasesFacade;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubNeedsRoleUseCaseImpl;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubPublicUseCaseImpl;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubUseCaseRequest;

class UseCasesFacadeTest extends TestCase
{
    public function target() : UseCasesFacade
    {
        return parent::target();
    }

    public function initialize(): void
    {
        $this->target()->addUseCase(new StubPublicUseCaseImpl());
        $this->target()->addUseCase(new StubNeedsRoleUseCaseImpl());
    }

    public function testUseCaseCanBeAdded() : void
    {
        $this->assertTrue($this->target()->hasUseCase('stubPublicUseCase'));
    }

    public function testGetUseCases() : void
    {
        $useCases = $this->target()->getUseCases();

        $this->assertCount(2, $useCases);

        while ($useCase = array_pop($useCases)) {
            $this->assertInstanceOf(AbstractUseCase::class, $useCase);
        }
    }

    public function testMethodFromUseCaseIsCorrectlyCalled() : void
    {
        $response = $this->target()->stubPublicUseCase(new StubUseCaseRequest(), $this->prophesy(Presenter::class)->reveal());
        $this->assertEquals('executed', $response->action);
    }

    public function testMethodCalledOnUnknownUseCaseThrowsABadFunctionCallException() : void
    {
        $this->expectException(BadFunctionCallException::class);
        $this->expectExceptionMessage('Solean\CleanProspecter\UseCase\UseCasesFacade::unknownUseCase()');

        $this->target()->unknownUseCase(new StubUseCaseRequest(), $this->prophesy(Presenter::class)->reveal());
    }

    public function testThrowUnauthorizedExceptionWhenConsumerHasNotAppropriateRole()
    {
        $roles = ['BAD_ROLE'];
        $this->prophesy(UseCaseConsumer::class)->getRoles()->shouldBeCalled()->willReturn($roles);

        $this->expectException(UnauthorizedException::class);

        $this->target()->stubNeedsRoleUseCase(new StubUseCaseRequest(), $this->prophesy(Presenter::class)->reveal(), $this->prophesy(UseCaseConsumer::class)->reveal());
    }

    /**
     * @dataProvider provideBadTypeParameters
     */
    public function testThrowBadFunctionCallWhenBadThirdParameterProvided($msg, $consumer)
    {
        $this->expectException(BadFunctionCallException::class);
        $this->expectExceptionMessage($msg);

        $this->target()->stubPublicUseCase(new StubUseCaseRequest(), $this->prophesy(Presenter::class)->reveal(), $consumer);
    }

    public function provideBadTypeParameters()
    {
        return [
            ' - Bad class object' => ['Argument 3 passed to stubPublicUseCase must be an instance of Solean\CleanProspecter\UseCase\UseCaseConsumer, instance of stdClass given', new \stdClass()],
            ' - Array' => ['Argument 3 passed to stubPublicUseCase must be an instance of Solean\CleanProspecter\UseCase\UseCaseConsumer, instance of array given', []],
            ' - Boolean' => ['Argument 3 passed to stubPublicUseCase must be an instance of Solean\CleanProspecter\UseCase\UseCaseConsumer, instance of boolean given', false],
            ' - String' => ['Argument 3 passed to stubPublicUseCase must be an instance of Solean\CleanProspecter\UseCase\UseCaseConsumer, instance of string given', 'string'],
        ];
    }
}
