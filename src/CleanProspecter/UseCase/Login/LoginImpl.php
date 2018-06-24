<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\Login;

use Solean\CleanProspecter\Entity\User;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\Exception\UseCase\BadCredentialException;

final class LoginImpl extends AbstractUseCase implements Login
{
    /**
     * @var UserGateway
     */
    private $userGateway;

    public function __construct(UserGateway $userGateway)
    {
        $this->userGateway = $userGateway;
    }

    public function execute(LoginRequest $request, LoginPresenter $presenter)
    {
        /**
         * @var ?User $user
         */
        $user = $this->userGateway->findOneBy(['userName' => $request->getLogin()]);

        if ($user && $this->isCorrectLogin($request, $user)) {
            return $presenter->present(new LoginResponse($user->getRoles(), $user->getUserName(), $request->getPassword()));
        }

        throw new BadCredentialException();
    }

    private function isCorrectLogin(LoginRequest $request, User $user): bool
    {
        $userTest = new User();
        $userTest->setPassword($request->getPassword());
        $userTest->setSalt($user->getSalt());

        return $userTest->encodePassword() === $user->getPassword();
    }
}
