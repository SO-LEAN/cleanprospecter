<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Double;

use SplFileInfo;
use Solean\Prospecting\Application\Port\FileStorage;

final class FakeFileStorage implements FileStorage
{
    /** @var string[] */
    public array $storedFiles = [];
    /** @var string[] */
    public array $removedUrls = [];

    public function store(SplFileInfo $file): string
    {
        $url = sprintf('http://storage.test/%s', $file->getFilename());
        $this->storedFiles[] = $url;
        return $url;
    }

    public function remove(string $url): void
    {
        $this->removedUrls[] = $url;
    }
}
