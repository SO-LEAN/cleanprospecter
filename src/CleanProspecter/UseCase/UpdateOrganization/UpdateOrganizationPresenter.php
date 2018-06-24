<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateOrganization;

interface UpdateOrganizationPresenter
{
    public function present(UpdateOrganizationResponse $response);
}
