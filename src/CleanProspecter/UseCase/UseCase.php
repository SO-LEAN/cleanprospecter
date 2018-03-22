<?php

namespace Solean\CleanProspecter\UseCase;

abstract class UseCase
{
    /**
     * empty mean everybody
     */
    public function executedBy()
    {
        return [];
    }
}