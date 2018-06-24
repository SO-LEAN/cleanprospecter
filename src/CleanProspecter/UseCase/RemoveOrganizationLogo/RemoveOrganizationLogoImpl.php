<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\RemoveOrganizationLogo;

use Solean\CleanProspecter\Exception\UseCase\UseCaseException;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\Traits\UseCase\UserNotifierTrait;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;

final class RemoveOrganizationLogoImpl extends AbstractUseCase implements RemoveOrganizationLogo
{
    use UserNotifierTrait;
    /**
     * @var OrganizationGateway
     */
    private $organizationGateway;
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(OrganizationGateway $organizationGateway, Storage $storage, UserNotifier $userNotifier)
    {
        $this->organizationGateway = $organizationGateway;
        $this->storage = $storage;
        $this->userNotifier = $userNotifier;
    }

    public function canBeExecutedBy(): array
    {
        return ['ROLE_PROSPECTOR'];
    }

    public function execute(RemoveOrganizationLogoRequest $request, RemoveOrganizationLogoPresenter $presenter, UseCaseConsumer $consumer)
    {
        $organization = $this->organizationGateway->get($request->getOrganizationId());
        $fileUrl = $organization->getLogo()->getUrl();

        if ($consumer->getOrganizationId() !== $organization->getOwnedBy()->getId()) {
            throw new UseCaseException(sprintf('Organization "%s" does not belong to your organization.', $organization->getOwnedBy()->getFullName()));
        }

        $organization->removeLogo();
        $this->storage->remove($fileUrl);
        $this->notifySuccess('Organization logo was removed !');

        return $presenter->present(new RemoveOrganizationLogoResponse($request->getOrganizationId()));
    }
}
