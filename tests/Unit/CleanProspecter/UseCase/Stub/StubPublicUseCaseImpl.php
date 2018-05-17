<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Stub;

use Solean\CleanProspecter\UseCase\AbstractUseCase;

class StubPublicUseCaseImpl extends AbstractUseCase
{
    private $roles;

    /**
     * StubPublicUseCaseImpl constructor.
     * @param $roles
     */
    public function __construct($roles = [])
    {
        $this->roles = $roles;
    }

    /**
     * empty mean everybody
     */
    public function canBeExecutedBy(): array
    {
        return $this->roles;
    }

    public function execute(StubUseCaseRequest $request, $presenter) : object
    {
        unset($request, $presenter);
        return (object)['action' => 'executed'];
    }
}
