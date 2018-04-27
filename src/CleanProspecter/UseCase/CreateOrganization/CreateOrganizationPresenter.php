<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\CreateOrganization;

interface CreateOrganizationPresenter
{
    public function present(CreateOrganizationResponse $response);
}
