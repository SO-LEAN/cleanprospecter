<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetOrganization;

final class GetOrganizationRequest
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * GetOrganizationRequest constructor.
     * @param mixed $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
