<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

interface CreateOrganization
{
    public function execute(CreateOrganizationRequest $request, CreateOrganizationPresenter $presenter): ?object;
}
