<?php

namespace Solean\CleanProspecter\UseCase\RemoveOrganizationLogo;

use Solean\CleanProspecter\UseCase\UseCaseConsumer;

interface RemoveOrganizationLogo
{
    public function execute(RemoveOrganizationLogoRequest $request, RemoveOrganizationLogoPresenter $presenter, UseCaseConsumer $consumer);
}
