<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\RemoveOrganizationLogo;

interface RemoveOrganizationLogoPresenter
{
    public function present(RemoveOrganizationLogoResponse $response);
}
