<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

use Solean\CleanProspecter\UseCase\UseCaseConsumer;

interface FindMyOwnOrganizations
{
    public function execute(FindMyOwnOrganizationsRequest $request, FindMyOwnOrganizationsPresenter $presenter, UseCaseConsumer $consumer);
}
