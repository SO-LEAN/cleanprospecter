<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetOrganization;

interface GetOrganizationPresenter
{
    public function present(GetOrganizationResponse $response);
}
