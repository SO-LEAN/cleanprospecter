<?php

namespace Solean\CleanProspecter\UseCase;

abstract class AbstractUseCase
{
    /**
     * empty mean everybody
     */
    public function executedBy()
    {
        return [];
    }
}
