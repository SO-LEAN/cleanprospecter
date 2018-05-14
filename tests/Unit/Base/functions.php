<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\Base;

use Tests\Unit\Solean\CleanProspecter\Builder\FileBuilder;
use Tests\Unit\Solean\CleanProspecter\Builder\PageBuilder;
use Tests\Unit\Solean\CleanProspecter\Builder\UserBuilder;
use Tests\Unit\Solean\CleanProspecter\Builder\AddressBuilder;
use Tests\Unit\Solean\CleanProspecter\Builder\GeoPointBuilder;
use Tests\Unit\Solean\CleanProspecter\Builder\OrganizationBuilder;

function aPage()
{
    return new PageBuilder();
}

function anAddress()
{
    return new AddressBuilder();
}
function aFile()
{
    return new FileBuilder();
}
function anOrganization()
{
    return new OrganizationBuilder();
}
function aUser()
{
    return new UserBuilder();
}
function aGeoPoint()
{
    return new GeoPointBuilder();
}
