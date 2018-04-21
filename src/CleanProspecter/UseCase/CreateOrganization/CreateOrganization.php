<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

use Solean\CleanProspecter\UseCase\Presenter;

interface CreateOrganization
{
    public function execute(CreateOrganizationRequest $request, Presenter $presenter): ?object;
}
