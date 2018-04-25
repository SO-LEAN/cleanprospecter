<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetOrganization;

use Solean\CleanProspecter\UseCase\Presenter;

interface GetOrganization
{
    public function execute(GetOrganizationRequest $request, Presenter $presenter): ?object;
}
