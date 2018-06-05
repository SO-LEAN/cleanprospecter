<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\RemoveOrganizationLogo;

final class RemoveOrganizationLogoRequest
{
    /**
     * @var mixed
     */
    private $organizationId;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }
}
