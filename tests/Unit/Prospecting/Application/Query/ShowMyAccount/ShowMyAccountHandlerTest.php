<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Query\ShowMyAccount;

use PHPUnit\Framework\Attributes\Test;
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

        $org = Organization::register(
            id: OrganizationId::fromString('100'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Owner Corp',
            form: 'SA',
            logo: Logo::create('http://storage.test/org-logo.png', 'png', 5000),
        );
        $this->organizationRepository->save($org);

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

    #[Test]
    public function shouldShowAccount(): void
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

    #[Test]
    public function shouldShowAccountWithPicture(): void
    {
        $user = $this->userRepository->ofId(UserId::fromString('1'));
        $user->attachPicture(Logo::create('http://storage.test/picture.jpg', 'jpg', 1200));
        $this->userRepository->save($user);

        ($this->handler)(new ShowMyAccountQuery('1', '100'), $this->presenter);

        $readModel = $this->presenter->readModel;
        $this->assertEquals('http://storage.test/picture.jpg', $readModel->pictureUrl);
        $this->assertEquals('jpg', $readModel->pictureExtension);
        $this->assertEquals(1200, $readModel->pictureSize);
    }

    #[Test]
    public function shouldThrowWhenUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);
        ($this->handler)(new ShowMyAccountQuery('999', '100'), $this->presenter);
    }

    #[Test]
    public function shouldThrowWhenOrganizationNotFound(): void
    {
        $this->expectException(OrganizationNotFoundException::class);
        ($this->handler)(new ShowMyAccountQuery('1', '999'), $this->presenter);
    }
}
