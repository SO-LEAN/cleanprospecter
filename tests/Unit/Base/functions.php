<?php

namespace Tests\Unit\Solean\Base;

use Tests\Unit\Solean\CleanProspecter\EntityBuilder\FileBuilder;
use Tests\Unit\Solean\CleanProspecter\EntityBuilder\UserBuilder;
use Tests\Unit\Solean\CleanProspecter\EntityBuilder\AddressBuilder;
use Tests\Unit\Solean\CleanProspecter\EntityBuilder\GeoPointBuilder;
use Tests\Unit\Solean\CleanProspecter\EntityBuilder\OrganizationBuilder;

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
