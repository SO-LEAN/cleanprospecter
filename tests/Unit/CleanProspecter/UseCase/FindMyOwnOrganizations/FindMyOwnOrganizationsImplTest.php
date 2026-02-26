<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

use Solean\CleanProspecter\Gateway\Entity\Page;
use Solean\CleanProspecter\Gateway\Entity\PageRequest;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsRequest;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsResponse;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsResponse\Organization;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsImpl;

use function Tests\Unit\Solean\Base\aPage;
use function Tests\Unit\Solean\Base\aFile;
use function Tests\Unit\Solean\Base\aGeoPoint;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\anOrganization;

class FindMyOwnOrganizationsImplTest extends UseCaseTest
{
    public function target() : FindMyOwnOrganizationsImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(OrganizationGateway::class)->reveal(),
        ];
    }

    public function testProspectorCanGetOrganization()
    {
        $this->assertArraySubset(['ROLE_PROSPECTOR'], $this->target()->canBeExecutedBy());
    }

    /**
     * @param FindMyOwnOrganizationsResponse $expectedResponse
     * @param Page $expectedPage
     *
     * @dataProvider provideExecute
     */
    public function testExecute(FindMyOwnOrganizationsResponse $expectedResponse, Page $expectedPage)
    {
        $request = new FindMyOwnOrganizationsRequest(1, 'my query', 10);

        $this->prophesy(UseCaseConsumer::class)
            ->getOrganizationId()
            ->shouldBeCalled()
            ->willReturn(222);

        $this->prophesy(OrganizationGateway::class)
            ->findPageByQuery(new PageRequest($request->getPage(), $request->getQuery(), $request->getMaxByPage(), ['ownedBy' => 222]))
            ->shouldBeCalled()
            ->willReturn($expectedPage);


        $result = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse), $this->prophesy(UseCaseConsumer::class)->reveal());

        $this->assertEquals($expectedResponse, $result);
    }

    public function provideExecute()
    {
        $page = aPage();

        (yield 'default' => [
            new FindMyOwnOrganizationsResponse(1, 25, 3, [
                new Organization(123, 'Organization Limited Company', null, null, null, null, null, null)
            ]),
            $page
                ->with('content', [
                    anOrganization()
                        ->withId()
                        ->build()
                    ])
                ->build()
        ]);
        (yield 'with address' => [
            new FindMyOwnOrganizationsResponse(1, 25, 3, [
                new Organization(123, 'Organization Limited Company', 'London', 'EN', 'SW1A 2AA', null, null, null)
            ]),
            $page->with('content', [
                anOrganization()
                    ->withId()
                    ->with('address', anAddress())
                    ->build()
                ])
                ->build()
        ]);
        (yield 'with logo' => [
            new FindMyOwnOrganizationsResponse(1, 25, 3, [
                new Organization(123, 'Organization Limited Company', null, null, null, 'http://url.net/image.png', null, null)
            ]),
            $page->with('content', [
                anOrganization()
                    ->withId()
                    ->with('logo', aFile()->withImageData())
                    ->build()
            ])
                ->build()
        ]);
        (yield 'with geoPoint' => [
            new FindMyOwnOrganizationsResponse(1, 25, 3, [
                new Organization(123, 'Organization Limited Company', null, null, null, null, 7.7663456, 48.5554971)
            ]),
            $page->with('content', [
                anOrganization()
                    ->withId()
                    ->with('geoPoint', aGeoPoint())
                    ->build()
            ])
                ->build()
        ]);
    }
}
