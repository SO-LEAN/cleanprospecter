<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Command\UpdateMyAccount;

use Solean\Prospecting\Application\Port\FileStorage;
use Solean\Prospecting\Application\Port\Notifier;
use Solean\Prospecting\Application\Port\TransactionManager;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PersonName;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;
use Solean\Prospecting\Domain\Model\User\UserId;
use Solean\Prospecting\Domain\Model\User\UserRepository;

final class UpdateMyAccountHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrganizationRepository $organizationRepository,
        private readonly FileStorage $fileStorage,
        private readonly TransactionManager $transactionManager,
        private readonly Notifier $notifier,
    ) {}

    public function __invoke(UpdateMyAccountCommand $command): void
    {
        $this->transactionManager->transactional(function () use ($command): void {
            $user = $this->userRepository->ofId(new UserId($command->userId));
            $organization = $this->organizationRepository->ofId(new OrganizationId($command->organizationId));

            $user->updateAccount(
                userName: $command->userName,
                name: new PersonName($command->firstName, $command->lastName),
                email: $command->email ? new Email($command->email) : null,
                phoneNumber: $command->phoneNumber ? new PhoneNumber($command->phoneNumber) : null,
                language: $command->language,
                password: $command->password,
            );

            if ($command->picture !== null) {
                $url = $this->fileStorage->store($command->picture);
                $user->attachPicture(new Logo($url, $command->picture->getExtension(), $command->picture->getSize()));
            }

            $organization->updateCorporateInfo(
                corporateName: $command->organizationCorporateName,
                form: $command->organizationForm,
                language: $command->language,
            );

            if ($command->organizationLogo !== null) {
                $url = $this->fileStorage->store($command->organizationLogo);
                $organization->attachLogo(new Logo($url, $command->organizationLogo->getExtension(), $command->organizationLogo->getSize()));
            }

            $this->userRepository->save($user);
            $this->organizationRepository->save($organization);
        });

        $this->notifier->success('User account information updated !');
    }
}
