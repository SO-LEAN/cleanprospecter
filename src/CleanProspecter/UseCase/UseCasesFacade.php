<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase;

use BadFunctionCallException;
use Solean\CleanProspecter\UseCase;

/**
 * @method login(UseCase\Login\LoginRequest $request)
 * @method findByUserName(UseCase\FindByUserName\FindByUserNameRequest $request)
 */
class UseCasesFacade
{
    /**
     * @var array
     */
    private $useCases;

    public function addUseCase(UseCase\UseCase $useCase): void
    {
        $this->useCases[lcfirst($this->getShortClassName($useCase))] = $useCase;
    }

    public function hasUseCase(string $name): bool
    {
        return array_key_exists($name, $this->useCases);
    }

    public function __call($name, $arguments)
    {
        if (!$this->hasUseCase($name)) {
            throw new BadFunctionCallException(sprintf('Call to undefined method %s::%s()', get_class($this), $name));
        }

        return call_user_func_array([$this->useCases[$name], 'execute'], $arguments);
    }

    private function getShortClassName($useCase)
    {
        return end(explode('\\', get_class($useCase)));
    }

}