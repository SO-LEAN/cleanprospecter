<?php

namespace Solean\CleanProspecter\UseCase\CreateOrganization;


class CreateOrganizationResponse
{
    /**
     * @var mixed
     */
    private $id;

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
