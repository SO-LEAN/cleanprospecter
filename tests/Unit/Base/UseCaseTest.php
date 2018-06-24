<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\Base;

use Prophecy\Argument;

abstract class UseCaseTest extends TestCase
{
    protected function getMockedPresenter($expected = null)
    {
        if (!$expected) {
            $this
                ->prophesy($this->getPresenterClassName())
                ->present(Argument::any())
                ->shouldNotBeCalled();
        } else {
            $this
                ->prophesy($this->getPresenterClassName())
                ->present($expected)
                ->shouldBeCalled()
                ->willReturnArgument(0);
        }

        return $this->prophesy($this->getPresenterClassName())->reveal();
    }

    protected function getPresenterClassName() : string
    {
        return sprintf('%sPresenter', preg_replace('/(^Tests\\\Unit\\\)|(ImplTest$)/', '', get_class($this)));
    }
}
