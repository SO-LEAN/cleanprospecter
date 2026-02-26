<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Query\ShowMyAccount;

use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Application\Query\ShowMyAccount\ShowMyAccountHandler;
use Solean\Prospecting\Application\Query\ShowMyAccount\ShowMyAccountQuery;
use Solean\Prospecting\Domain\Exception\OrganizationNotFoundException;
use Solean\Prospecting\Domain\Exception\UserNotFoundException;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PersonName;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;
use Solean\Prospecting\Domain\Model\User\User;
use Solean\Prospecting\Domain\Model\User\UserId;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryOrganizationRepository;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryUserRepository;
use Tests\Unit\Prospecting\Double\CollectingShowMyAccountPresenter;

final class ShowMyAccountHandlerTest extends TestCase
{
    private InMemoryUserRepository $userRepository;
    private InMemoryOrganizationRepository $organizationRepository;
    private CollectingShowMyAccountPresenter $presenter;
    private ShowMyAccountHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->organizationRepository = new InMemoryOrganizationRepository();
        $this->presenter = new CollectingShowMyAccountPresenter();

        $this->handler = new ShowMyAccountHandler(
            $this->userRepository,
            $this->organizationRepository,
        );

        // Seed organization
        $org = Organization::register(
            id: new OrganizationId('100'),
            ownerId: new OrganizationId('100'),
            corporateName: 'Owner Corp',
            form: 'SA',
            logo: new Logo('http://storage.test/org-logo.png', 'png', 5000),
        );
        $this->organizationRepository->save($org);

        // Seed user
        $user = User::create(
            id: new UserId('1'),
            userName: 'john.doe',
            organizationId: new OrganizationId('100'),
            name: new PersonName('John', 'Doe'),
            email: new Email('john@example.com'),
            phoneNumber: new PhoneNumber('0123456789'),
            language: 'EN',
        );
        $this->userRepository->save($user);
    }

    public function testShowAccount(): void
    {
        ($this->handler)(new ShowMyAccountQuery('1', '100'), $this->presenter);

        $readModel = $this->presenter->readModel;
        $this->assertNotNull($readModel);
        $this->assertEquals('john.doe', $readModel->userName);
        $this->assertEquals('John', $readModel->firstName);
        $this->assertEquals('Doe', $readModel->lastName);
        $this->assertEquals('john@example.com', $readModel->email);
        $this->assertEquals('0123456789', $readModel->phoneNumber);
        $this->assertEquals('EN', $readModel->language);
        $this->assertEquals('Owner Corp', $readModel->organizationCorporateName);
        $this->assertEquals('SA', $readModel->organizationForm);
        $this->assertEquals('http://storage.test/org-logo.png', $readModel->organizationLogoUrl);
        $this->assertEquals('png', $readModel->organizationLogoExtension);
        $this->assertEquals(5000, $readModel->organizationLogoSize);
    }

    public function testShowAccountWithPicture(): void
    {
        $user = $this->userRepository->ofId(new UserId('1'));
        $user->attachPicture(new Logo('http://storage.test/picture.jpg', 'jpg', 1200));
        $this->userRepository->save($user);

        ($this->handler)(new ShowMyAccountQuery('1', '100'), $this->presenter);

        $readModel = $this->presenter->readModel;
        $this->assertEquals('http://storage.test/picture.jpg', $readModel->pictureUrl);
        $this->assertEquals('jpg', $readModel->pictureExtension);
        $this->assertEquals(1200, $readModel->pictureSize);
    }

    public function testThrowsWhenUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);
        ($this->handler)(new ShowMyAccountQuery('999', '100'), $this->presenter);
    }

    public function testThrowsWhenOrganizationNotFound(): void
    {
        $this->expectException(OrganizationNotFoundException::class);
        ($this->handler)(new ShowMyAccountQuery('1', '999'), $this->presenter);
    }
}
