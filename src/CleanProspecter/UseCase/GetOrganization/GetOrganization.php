<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetOrganization;

interface GetOrganization
{
    public function execute(GetOrganizationRequest $request, GetOrganizationPresenter $presenter);
}
