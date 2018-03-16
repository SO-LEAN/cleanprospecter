<?php
namespace Solean\CleanProspector\UseCase\Login;

use Solean\CleanProspector\Exception\UnauthorizedException;
use Solean\CleanProspector\Presenter;
use Solean\CleanProspector\Entity\User;
use Solean\CleanProspector\Gateway\Database\UserGateway;

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