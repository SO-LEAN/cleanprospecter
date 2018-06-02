<?php

declare(strict_types=1);

namespace Solean\CleanProspecter\UseCase\UpdateMyAccountInformation;

use Exception;
use Solean\CleanProspecter\Entity\File;
use Solean\CleanProspecter\Entity\User;
use Solean\CleanProspecter\Exception\Gateway;
use Solean\CleanProspecter\Exception\Entity\ValidationException;
use Solean\CleanProspecter\Exception\UseCase\UseCaseException;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Entity\Organization;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\UseCase\AbstractUseCase;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use Solean\CleanProspecter\Gateway\Entity\Transaction;
use Solean\CleanProspecter\Gateway\Entity\UserGateway;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\Exception\UseCase\UniqueConstraintViolationException;

final class UpdateMyAccountInformationImpl extends AbstractUseCase implements UpdateMyAccountInformation
{
    /**
     * @var OrganizationGateway
     */
    private $organizationGateway;
    /**
     * @var UserGateway
     */
    private $userGateway;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var UserNotifier
     */
    private $userNotifier;

    public function __construct(
        OrganizationGateway $organizationGateway,
        UserGateway $userGateway,
        Transaction $transaction,
        Storage $storage,
        UserNotifier $userNotifier
    ) {
        $this->organizationGateway = $organizationGateway;
        $this->userGateway = $userGateway;
        $this->transaction = $transaction;
        $this->storage = $storage;
        $this->userNotifier = $userNotifier;
    }

    public function canBeExecutedBy(): array
    {
        return ['ROLE_USER'];
    }

    public function execute(UpdateMyAccountInformationRequest $request, UpdateMyAccountInformationPresenter $presenter, UseCaseConsumer $consumer): ?object
    {

        $this->transaction->begin();

        $user = $this->userGateway->get($consumer->getUserId());
        $organization = $this->organizationGateway->get($consumer->getOrganizationId());

        $this->alterUser($request, $user);
        $this->alterOrganization($request, $organization);
        $this->handleTransaction($request, $user, $organization);
        $this->notifySuccess('User account information updated !');

        $response = $this->buildResponse($organization, $user);

        return $presenter->present($response);
    }

    private function alterUser(UpdateMyAccountInformationRequest $request, User $user): User
    {
        $user->setPhoneNumber($request->getPhoneNumber());
        $user->setEmail($request->getEmail());
        $user->setLanguage($request->getLanguage());
        $user->setUserName($request->getUserName());
        $user->setFirstName($request->getFirstName());
        $user->setLastName($request->getLastName());

        if ($request->getPassword()) {
            $user->setPassword($request->getPassword());
            $user->encodePassword();
        }

        if ($request->getPicture()) {
            $user->setPicture(File::fromValues($this->storage->add($request->getPicture()), $request->getPicture()->getExtension(), $request->getPicture()->getSize()));
        }

        return $user;
    }

    private function alterOrganization(UpdateMyAccountInformationRequest $request, Organization $organization): Organization
    {
        $organization->setLanguage($request->getLanguage());
        $organization->setCorporateName($request->getOrganizationCorporateName());
        $organization->setForm($request->getOrganizationForm());

        if ($request->getOrganizationLogo()) {
            $organization->setLogo(File::fromValues($this->storage->add($request->getOrganizationLogo()), $request->getOrganizationLogo()->getExtension(), $request->getOrganizationLogo()->getSize()));
        }

        return $organization;
    }

    private function handleTransaction(UpdateMyAccountInformationRequest $request, User $user, Organization $organization): void
    {
        try {
            $this->updateUser($user);
            $this->updateOrganization($organization);

            $this->transaction->commit();
        } catch (Exception $e) {
            $this->transaction->rollback();
            throw $e;
        };
    }

    private function updateUser(User $user): User
    {
        try {
            return $this->userGateway->update($user->getId(), $user);
        } catch (Gateway\UniqueConstraintViolationException $e) {
            throw new UniqueConstraintViolationException('Email already used', 412, $e, ['email' => sprintf('Email "%s" already used', $user->getEmail())]);
        }
    }

    private function updateOrganization(Organization $organization): Organization
    {
        return $this->organizationGateway->update($organization->getId(), $organization);
    }

    private function buildResponse(Organization $organization, User $user): UpdateMyAccountInformationResponse
    {
        return new UpdateMyAccountInformationResponse(
            $user->getUserName(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getPicture() ? $user->getPicture()->getUrl() : null,
            $user->getPicture() ? $user->getPicture()->getExtension() : null,
            $user->getPicture() ? $user->getPicture()->getSize() : null,
            $user->getPhoneNumber(),
            $user->getEmail(),
            $user->getLanguage(),
            $organization->getCorporateName(),
            $organization->getForm(),
            $organization->getLogo() ? $organization->getLogo()->getUrl() : null,
            $organization->getLogo() ? $organization->getLogo()->getExtension() : null,
            $organization->getLogo() ? $organization->getLogo()->getSize() : null
        );
    }

    private function notifySuccess(string $msg)
    {
        $this->userNotifier->addSuccess($msg);
    }
}
