<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\Login;

use Solean\CleanProspecter\Entity\User;
use Solean\CleanProspecter\UseCase\UseCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\Gateway\Database\UserGateway;
use Solean\CleanProspecter\Exception\UseCase\BadCredentialException;

class Login extends UseCase
{
    /**
     * @var UserGateway
     */
    private $userGateway;
    /**
     * @var Presenter
     */
    private $presenter;

    public function __construct(UserGateway $userGateway, Presenter $presenter)
    {
        $this->userGateway = $userGateway;
        $this->presenter = $presenter;
    }

    public function execute(LoginRequest $request) : object
    {
        /**
         * @var ?User $user
         */
        $user = $this->userGateway->findOneBy(['userName' => $request->getLogin()]);

        if ($user && $this->isCorrectLogin($request, $user)) {
            return $this->presenter->present(new LoginResponse($user->getRoles(), $user->getUserName(), $request->getPassword()));
        }

        throw new BadCredentialException();
    }

    private function encodePassword(string $password, string $salt) : string
    {
        return md5(sprintf('%s%s', $password, $salt));
    }

    private function isCorrectLogin(LoginRequest $request, User $user): bool
    {
        return $this->encodePassword($request->getPassword(), $user->getSalt()) === $user->getPassword();
    }
}