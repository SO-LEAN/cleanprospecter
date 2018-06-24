<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindMyOwnOrganizations;

interface FindMyOwnOrganizationsPresenter
{
    public function present(FindMyOwnOrganizationsResponse $response);
}
