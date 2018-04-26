<?php

namespace Solean\CleanProspecter\UseCase;

abstract class AbstractUseCase
{
    /**
     * empty mean everybody
     */
    public function canBeExecutedBy(): array
    {
        return [];
    }
}
