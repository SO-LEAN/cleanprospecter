<?php

declare(strict_types=1);

namespace Solean\Prospecting\Application\Port;

use SplFileInfo;

interface FileStorage
{
    public function store(SplFileInfo $file): string;

    public function remove(string $url): void;
}
