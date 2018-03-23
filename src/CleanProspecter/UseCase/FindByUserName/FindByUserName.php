<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindByUserName;


use Solean\CleanProspecter\UseCase\UseCase;
use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\Gateway\Database\UserGateway;

class FindByUserName extends UseCase
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

    public function execute(FindByUserNameRequest $request)
    {
        /**
         * @var ?User $user
         */
        $user = $this->userGateway->findOneBy(['userName' => $request->getLogin()]);

        if ($user) {
            return $this->presenter->present(
                new FindByUserNameResponse($user->getRoles(), $user->getUserName(), $request->getPassword())
            );
        }

        return null;
    }
}