<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindByUserName;

use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;

final class FindByUserNameImpl extends AbstractUseCase implements FindByUserName
{
    /**
     * @var UserGateway
     */
    private $userGateway;

    public function __construct(UserGateway $userGateway)
    {
        $this->userGateway = $userGateway;
    }

    public function execute(FindByUserNameRequest $request, Presenter $presenter): ?object
    {
        /**
         * @var ?User $user
         */
        $user = $this->userGateway->findOneBy(['userName' => $request->getLogin()]);

        if ($user) {
            return $presenter->present(
                new FindByUserNameResponse($user->getRoles(), $user->getUserName(), $user->getPassword())
            );
        }

        return null;
    }
}
