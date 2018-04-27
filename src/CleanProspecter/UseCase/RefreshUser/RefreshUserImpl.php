<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\RefreshUser;

use Solean\CleanProspecter\UseCase\Presenter;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;

final class RefreshUserImpl extends AbstractUseCase implements RefreshUser
{
    /**
     * @var UserGateway
     */
    private $userGateway;

    public function __construct(UserGateway $userGateway)
    {
        $this->userGateway = $userGateway;
    }

    public function execute(RefreshUserRequest $request, Presenter $presenter): ?object
    {
        /**
         * @var ?User $user
         */
        $user = $this->userGateway->findOneBy(['userName' => $request->getLogin()]);

        if ($user) {
            return $presenter->present(
                new RefreshUserResponse($user->getId(), $user->getRoles(), $user->getUserName(), $user->getPassword(), $user->getOrganization()->getId())
            );
        }

        return null;
    }
}
