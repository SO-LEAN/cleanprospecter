<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Command\UpdateMyAccount;

use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Application\Command\UpdateMyAccount\UpdateMyAccountCommand;
use Solean\Prospecting\Application\Command\UpdateMyAccount\UpdateMyAccountHandler;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\User\User;
use Solean\Prospecting\Domain\Model\User\UserId;
use Solean\Prospecting\Domain\Model\Shared\PersonName;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryOrganizationRepository;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use Tests\Unit\Prospecting\Double\FakeFileStorage;
use Tests\Unit\Prospecting\Double\FakeTransactionManager;
use Tests\Unit\Prospecting\Double\SpyNotifier;

final class UpdateMyAccountHandlerTest extends TestCase
{
    private InMemoryUserRepository $userRepository;
    private InMemoryOrganizationRepository $organizationRepository;
    private FakeFileStorage $fileStorage;
    private FakeTransactionManager $transactionManager;
    private SpyNotifier $notifier;
    private UpdateMyAccountHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->organizationRepository = new InMemoryOrganizationRepository();
        $this->fileStorage = new FakeFileStorage();
        $this->transactionManager = new FakeTransactionManager();
        $this->notifier = new SpyNotifier();

        $this->handler = new UpdateMyAccountHandler(
            $this->userRepository,
            $this->organizationRepository,
            $this->fileStorage,
            $this->transactionManager,
            $this->notifier,
        );

        // Seed data
        $owner = Organization::register(
            id: OrganizationId::fromString('100'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Owner Corp',
        );
        $this->organizationRepository->save($owner);

        $user = User::create(
            id: UserId::fromString('1'),
            userName: 'john.doe',
            organizationId: OrganizationId::fromString('100'),
            name: PersonName::fromParts('John', 'Doe'),
            email: Email::fromString('john@example.com'),
            phoneNumber: PhoneNumber::fromString('0123456789'),
            language: 'EN',
        );
        $this->userRepository->save($user);
    }

    public function testUpdateAccountBasicInfo(): void
    {
        $command = new UpdateMyAccountCommand(
            userId: '1',
            organizationId: '100',
            userName: 'jane.doe',
            firstName: 'Jane',
            lastName: 'Doe',
            phoneNumber: '9876543210',
            email: 'jane@example.com',
            language: 'FR',
            organizationCorporateName: 'New Corp',
            organizationForm: 'SARL',
        );

        ($this->handler)($command);

        $updatedUser = $this->userRepository->ofId(UserId::fromString('1'));
        $this->assertEquals('jane.doe', $updatedUser->userName());
        $this->assertEquals('Jane', $updatedUser->name()->firstName);
        $this->assertTrue($this->transactionManager->committed);
        $this->assertCount(1, $this->notifier->successes);
        $this->assertEquals('User account information updated !', $this->notifier->successes[0]);

        $updatedOrg = $this->organizationRepository->ofId(OrganizationId::fromString('100'));
        $this->assertEquals('New Corp', $updatedOrg->corporateName());
        $this->assertEquals('SARL', $updatedOrg->form());
    }
}
