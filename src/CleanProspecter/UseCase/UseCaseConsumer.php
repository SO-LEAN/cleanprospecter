<?php

declare(strict_types = 1);

namespace Solean\CleanProspecter\UseCase;

interface UseCaseConsumer
{
    public function getRoles(): array;
    public function getUserId();
}
