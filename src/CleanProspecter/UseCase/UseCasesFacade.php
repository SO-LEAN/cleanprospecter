<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase;

use BadFunctionCallException;
use Solean\CleanProspecter\UseCase;
use Solean\CleanProspecter\Exception\UseCase\UnauthorizedException;

/**
 * @method login(UseCase\Login\LoginRequest $request, Presenter $presenter, ?UseCaseConsumer $consumer)
 * @method findByUserName(UseCase\RefreshUser\RefreshUserRequest $request, Presenter $presenter, ?UseCaseConsumer $consumer)
 * @method createOrganization(UseCase\CreateOrganization\CreateOrganizationRequest $request, Presenter $presenter, ?UseCaseConsumer $consumer)
 * @method getOrganization(UseCase\GetOrganization\GetOrganizationRequest $request, Presenter $presenter, ?UseCaseConsumer $consumer)
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
        $this->checkCalledMethod($name, $arguments);
        $this->checkAccess($arguments, $name);

        return call_user_func_array([$this->useCases[$name], 'execute'], $arguments);
    }

    private function getShortClassName(object $useCase): string
    {
        $shortName = explode('\\', preg_replace('/(Impl$)/', '', get_class($useCase)));

        return end($shortName);
    }

    private function checkCalledMethod(string $name, array $arguments): void
    {
        if (!$this->hasUseCase($name)) {
            throw new BadFunctionCallException(sprintf('Call to undefined method %s::%s()', get_class($this), $name));
        }

        if (isset($arguments[2]) && !($arguments[2] instanceof UseCaseConsumer)) {
            $type = is_object($arguments[2]) ? get_class($arguments[2]) : getType($arguments[2]);

            throw new BadFunctionCallException(sprintf('Argument 3 passed to %s must be an instance of %s, instance of %s given', $name, UseCaseConsumer::class, $type));
        }
    }

    private function checkAccess(array $arguments, string $name): void
    {
        $useCase = $this->useCases[$name];
        $consumerRoles = $this->getConsumerRoles($arguments);

        $isAdmin = in_array('ROLE_ADMIN', $consumerRoles);
        $hasAppropriateRole = !empty(array_intersect($consumerRoles, $useCase->canBeExecutedBy()));
        $isPublicUseCase = empty($useCase->canBeExecutedBy());

        if ($isAdmin || $hasAppropriateRole || $isPublicUseCase) {
            return;
        }

        throw new UnauthorizedException();
    }

    private function getConsumerRoles(array $arguments): array
    {
        if (isset($arguments[2])) {
            $roles = $arguments[2]->getRoles();
        } else {
            $roles = [];
        }

        return $roles;
    }

}
