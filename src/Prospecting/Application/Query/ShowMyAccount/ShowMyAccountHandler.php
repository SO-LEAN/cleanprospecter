<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Query\ShowMyAccount;

use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;
use Solean\Prospecting\Domain\Model\User\UserId;
use Solean\Prospecting\Domain\Model\User\UserRepository;

final class ShowMyAccountHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrganizationRepository $organizationRepository,
    ) {}

    public function __invoke(ShowMyAccountQuery $query, ShowMyAccountPresenter $presenter): void
    {
        $user = $this->userRepository->ofId(UserId::fromString($query->userId));
        $organization = $this->organizationRepository->ofId(OrganizationId::fromString($query->organizationId));

        $readModel = new AccountReadModel(
            userName: $user->userName(),
            firstName: $user->name()?->firstName,
            lastName: $user->name()?->lastName,
            pictureUrl: $user->picture()?->url,
            pictureExtension: $user->picture()?->extension,
            pictureSize: $user->picture()?->size,
            phoneNumber: $user->phoneNumber()?->value,
            email: $user->email()?->value,
            language: $user->language(),
            organizationCorporateName: $organization->corporateName(),
            organizationForm: $organization->form(),
            organizationLogoUrl: $organization->logo()?->url,
            organizationLogoExtension: $organization->logo()?->extension,
            organizationLogoSize: $organization->logo()?->size,
        );

        $presenter->present($readModel);
    }
}
