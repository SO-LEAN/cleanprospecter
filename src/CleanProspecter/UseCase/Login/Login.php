<?php
namespace Solean\CleanProspecter\UseCase\Login;

use Solean\CleanProspecter\Exception\UnauthorizedException;
use Solean\CleanProspecter\Presenter;
use Solean\CleanProspecter\Entity\User;
use Solean\CleanProspecter\Gateway\Database\UserGateway;

final class Login
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

    public function execute(LoginRequest $request)
    {
        /**
         * @var User $user
         */
        $user = $this->userGateway->findBy(['login' => $request->getLogin()]);

        if ($this->encodePassword($request->getPassword(), $user->getSalt()) !== $user->getPassword()) {
            return $this->presenter->present(new LoginResponse($user->getRoles(), $user->getPassword(), $user->getUserName()));
        }

        throw new UnauthorizedException();
    }

    private function encodePassword(string $password, string $salt) : string
    {
        return md5(sprintf('%s%s', $password, $salt));
    }
}