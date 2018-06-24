<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

use Solean\CleanProspecter\Gateway\Entity\Page;
use Solean\CleanProspecter\Gateway\Entity\PageRequest;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsResponse;
use Solean\CleanProspecter\UseCase\UseCaseConsumer;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsImpl;
use Tests\Unit\Solean\CleanProspecter\UseCase\FindMyOwnOrganizations\FindMyOwnOrganizationsResponse\OrganizationBuilder;

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
        $request = aRequest()
            ->build();

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
        $resp = aResponse();
        $page = aPage();

        (yield 'default' => [
            $resp
                ->with('organizations', [
                    aDtoOrganization()
                        ->build()
                    ])
                ->build(),
            $page
                ->with('content', [
                    anOrganization()
                        ->withId()
                        ->build()
                    ])
                ->build()
        ]);
        (yield 'with address' => [
            $resp
                ->with('organizations', [
                    aDtoOrganization()
                        ->withAddress()
                        ->build()
                    ])
                ->build(),
            $page->with('content', [
                anOrganization()
                    ->withId()
                    ->with('address', anAddress())
                    ->build()
                ])
                ->build()
        ]);
        (yield 'with logo' => [
            $resp
                ->with('organizations', [
                    aDtoOrganization()
                        ->withLogo()
                        ->build()
                ])
                ->build(),
            $page->with('content', [
                anOrganization()
                    ->withId()
                    ->with('logo', aFile()->withImageData())
                    ->build()
            ])
                ->build()
        ]);
        (yield 'with geoPoint' => [
            $resp
                ->with('organizations', [
                    aDtoOrganization()
                        ->withGeoPoint()
                        ->build()
                ])
                ->build(),
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

function aRequest()
{
    return new FindMyOwnOrganizationsRequestBuilder();
}
function aResponse()
{
    return new FindMyOwnOrganizationsResponseBuilder();
}

function aDtoOrganization()
{
    return new OrganizationBuilder();
}
