<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\Stub;

use Solean\CleanProspecter\UseCase\UseCase;

class StubUseCase extends UseCase
{
    public function execute(StubUseCaseRequest $request)
    {
        unset($request);

        return 'executed';
    }
}