<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Stub;

use Solean\CleanProspecter\UseCase\AbstractUseCase;

class StubPublicUseCaseImpl extends AbstractUseCase
{
    public function execute(StubUseCaseRequest $request, $presenter) : object
    {
        unset($request, $presenter);
        return (object)['action' => 'executed'];
    }
}
