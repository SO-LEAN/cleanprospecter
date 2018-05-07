<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\FindOrganization;

interface FindOrganizationPresenter
{
    public function present(FindOrganizationResponse $response);
}
