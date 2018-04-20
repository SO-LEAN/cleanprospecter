<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\Login;

use Solean\CleanProspecter\Entity\User;
use Solean\CleanProspecter\UseCase\Presenter;
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

    public function execute(LoginRequest $request, Presenter $presenter): object
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

    private function encodePassword(string $password, string $salt): string
    {
        return md5(sprintf('%s%s', $password, $salt));
    }

    private function isCorrectLogin(LoginRequest $request, User $user): bool
    {
        return $this->encodePassword($request->getPassword(), $user->getSalt()) === $user->getPassword();
    }
}
