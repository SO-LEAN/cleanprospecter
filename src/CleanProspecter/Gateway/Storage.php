<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway;

use SplFileInfo;

interface Storage
{
    public function add(SplFileInfo $file): string;
    public function remove(string $url);
}
