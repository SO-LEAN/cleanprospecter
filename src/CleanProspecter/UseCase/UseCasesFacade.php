<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase;

use BadFunctionCallException;
use Solean\CleanProspecter\UseCase;

/**
 * @method login(UseCase\Login\LoginRequest $request, Presenter $presenter)
 * @method findByUserName(UseCase\FindByUserName\FindByUserNameRequest $request, Presenter $presenter)
 * @method createOrganization(UseCase\CreateOrganization\CreateOrganizationRequest $request, Presenter $presenter)
 */
class UseCasesFacade
{
    /**
     * @var array
     */
    private $useCases;

    public function __construct()
    {
        $this->useCases = [];
    }

    public function addUseCase(AbstractUseCase $useCase): void
    {
        $this->useCases[lcfirst($this->getShortClassName($useCase))] = $useCase;
    }

    public function hasUseCase(string $name): bool
    {
        return array_key_exists($name, $this->useCases);
    }

    public function __call(string $name, array $arguments)
    {
        if (!$this->hasUseCase($name)) {
            throw new BadFunctionCallException(sprintf('Call to undefined method %s::%s()', get_class($this), $name));
        }

        return call_user_func_array([$this->useCases[$name], 'execute'], $arguments);
    }

    private function getShortClassName(object $useCase): string
    {
        $shortName = explode('\\', preg_replace('/(Impl$)/', '', get_class($useCase)));

        return end($shortName);
    }
}
