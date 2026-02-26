<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Application\Query\ShowOrganization;

use PHPUnit\Framework\TestCase;
use Solean\Prospecting\Application\Query\ShowOrganization\ShowOrganizationHandler;
use Solean\Prospecting\Application\Query\ShowOrganization\ShowOrganizationQuery;
use Solean\Prospecting\Domain\Exception\OrganizationNotFoundException;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\Address;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;
use Solean\Prospecting\Infrastructure\Persistence\InMemory\InMemoryOrganizationRepository;
use Tests\Unit\Prospecting\Double\CollectingShowOrganizationPresenter;

final class ShowOrganizationHandlerTest extends TestCase
{
    private InMemoryOrganizationRepository $organizationRepository;
    private CollectingShowOrganizationPresenter $presenter;
    private ShowOrganizationHandler $handler;

    protected function setUp(): void
    {
        $this->organizationRepository = new InMemoryOrganizationRepository();
        $this->presenter = new CollectingShowOrganizationPresenter();

        $this->handler = new ShowOrganizationHandler(
            $this->organizationRepository,
        );

        // Seed owner
        $owner = Organization::register(
            id: new OrganizationId('100'),
            ownerId: new OrganizationId('100'),
            corporateName: 'Owner Corp',
        );
        $this->organizationRepository->save($owner);
    }

    public function testShowMinimalOrganization(): void
    {
        $org = Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
            corporateName: 'ACME',
        );
        $this->organizationRepository->save($org);

        ($this->handler)(new ShowOrganizationQuery('1'), $this->presenter);

        $readModel = $this->presenter->readModel;
        $this->assertNotNull($readModel);
        $this->assertEquals('1', $readModel->id);
        $this->assertEquals('100', $readModel->ownerId);
        $this->assertEquals('ACME', $readModel->corporateName);
        $this->assertNull($readModel->email);
        $this->assertNull($readModel->phoneNumber);
        $this->assertNull($readModel->logoUrl);
        $this->assertNull($readModel->holdingId);
    }

    public function testShowFullOrganization(): void
    {
        $org = Organization::register(
            id: new OrganizationId('1'),
            ownerId: new OrganizationId('100'),
            corporateName: 'ACME Corp',
            email: new Email('info@acme.com'),
            phoneNumber: new PhoneNumber('0123456789'),
            language: 'FR',
            form: 'SARL',
            type: 'Direct',
            observations: 'Some notes',
            address: new Address('20 avenue du Neuhof', '67100', 'Strasbourg', 'FR'),
            logo: new Logo('http://storage.test/logo.png', 'png', 2500),
            holdingId: new OrganizationId('100'),
        );
        $org->pinpoint(new GeoPoint(7.7663456, 48.5554971));
        $this->organizationRepository->save($org);

        ($this->handler)(new ShowOrganizationQuery('1'), $this->presenter);

        $readModel = $this->presenter->readModel;
        $this->assertNotNull($readModel);
        $this->assertEquals('1', $readModel->id);
        $this->assertEquals('ACME Corp', $readModel->corporateName);
        $this->assertEquals('info@acme.com', $readModel->email);
        $this->assertEquals('0123456789', $readModel->phoneNumber);
        $this->assertEquals('FR', $readModel->language);
        $this->assertEquals('SARL', $readModel->form);
        $this->assertEquals('Direct', $readModel->type);
        $this->assertEquals('Some notes', $readModel->observations);
        $this->assertEquals('20 avenue du Neuhof', $readModel->street);
        $this->assertEquals('67100', $readModel->postalCode);
        $this->assertEquals('Strasbourg', $readModel->city);
        $this->assertEquals('FR', $readModel->country);
        $this->assertEquals(7.7663456, $readModel->longitude);
        $this->assertEquals(48.5554971, $readModel->latitude);
        $this->assertEquals('http://storage.test/logo.png', $readModel->logoUrl);
        $this->assertEquals('png', $readModel->logoExtension);
        $this->assertEquals(2500, $readModel->logoSize);
        $this->assertEquals('100', $readModel->holdingId);
    }

    public function testThrowsWhenOrganizationNotFound(): void
    {
        $this->expectException(OrganizationNotFoundException::class);
        ($this->handler)(new ShowOrganizationQuery('999'), $this->presenter);
    }
}
