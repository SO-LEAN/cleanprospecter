<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\FindOrganization;

use Solean\CleanProspecter\Gateway\Entity\Page;
use Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationResponse;
use function Tests\Unit\Solean\Base\aFile;
use function Tests\Unit\Solean\Base\anAddress;
use Tests\Unit\Solean\Base\UseCaseTest;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationImpl;
use Tests\Unit\Solean\CleanProspecter\UseCase\FindOrganization\FindOrganizationResponse\OrganizationBuilder;

use function Tests\Unit\Solean\Base\aPage;
use function Tests\Unit\Solean\Base\anOrganization;

class FindOrganizationImplTest extends UseCaseTest
{
    public function target() : FindOrganizationImpl
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
     * @param FindOrganizationResponse $expectedResponse
     * @param Page $expectedPage
     *
     * @dataProvider provideExecute
     */
    public function testExecute(FindOrganizationResponse $expectedResponse, Page $expectedPage)
    {
        $request = aRequest()
            ->build();

        $this->prophesy(OrganizationGateway::class)
            ->findPageByQuery($request->getPage(), $request->getQuery(), $request->getMaxByPage())
            ->shouldBeCalled()
            ->willReturn($expectedPage);

        $result = $this->target()->execute($request, $this->getMockedPresenter($expectedResponse));

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
    }
}

function aRequest()
{
    return new FindOrganizationRequestBuilder();
}
function aResponse()
{
    return new FindOrganizationResponseBuilder();
}

function aDtoOrganization()
{
    return new OrganizationBuilder();
}
