<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Command\RemoveOrganizationLogo;

use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Application\Command\RemoveOrganizationLogo\RemoveOrganizationLogoCommand;
use Solean\Prospecting\Application\Command\RemoveOrganizationLogo\RemoveOrganizationLogoHandler;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryOrganizationRepository;
use Tests\Unit\Prospecting\Double\FakeFileStorage;
use Tests\Unit\Prospecting\Double\SpyNotifier;

final class RemoveOrganizationLogoHandlerTest extends TestCase
{
    private InMemoryOrganizationRepository $organizationRepository;
    private FakeFileStorage $fileStorage;
    private SpyNotifier $notifier;
    private RemoveOrganizationLogoHandler $handler;

    protected function setUp(): void
    {
        $this->organizationRepository = new InMemoryOrganizationRepository();
        $this->fileStorage = new FakeFileStorage();
        $this->notifier = new SpyNotifier();

        $this->handler = new RemoveOrganizationLogoHandler(
            $this->organizationRepository,
            $this->fileStorage,
            $this->notifier,
        );

        // Seed organization with logo
        $owner = Organization::register(
            id: OrganizationId::fromString('100'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'Owner Corp',
        );
        $this->organizationRepository->save($owner);

        $org = Organization::register(
            id: OrganizationId::fromString('1'),
            ownerId: OrganizationId::fromString('100'),
            corporateName: 'ACME',
            logo: Logo::create('http://storage.test/logo.png', 'png', 2500),
        );
        $this->organizationRepository->save($org);
    }

    public function testRemoveLogo(): void
    {
        $command = new RemoveOrganizationLogoCommand(organizationId: '1');

        ($this->handler)($command);

        $org = $this->organizationRepository->ofId(OrganizationId::fromString('1'));
        $this->assertNull($org->logo());
        $this->assertCount(1, $this->fileStorage->removedUrls);
        $this->assertEquals('http://storage.test/logo.png', $this->fileStorage->removedUrls[0]);
        $this->assertCount(1, $this->notifier->successes);
    }
}
