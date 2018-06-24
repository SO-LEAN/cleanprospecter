<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateOrganization;

interface UpdateOrganization
{
    public function execute(UpdateOrganizationRequest $request, UpdateOrganizationPresenter $presenter): ?object;
}
