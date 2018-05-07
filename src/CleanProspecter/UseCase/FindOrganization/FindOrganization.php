<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindOrganization;

interface FindOrganization
{
    public function execute(FindOrganizationRequest $request, FindOrganizationPresenter $presenter);
}
