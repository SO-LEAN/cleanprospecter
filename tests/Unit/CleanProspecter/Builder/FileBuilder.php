<?php

declare(strict_types=1);

namespace Tests\Unit\Solean\CleanProspecter\Builder;

use Tests\Unit\Solean\Base\Builder;
use Solean\CleanProspecter\Entity\File;

class FileBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct();
        $this->withRegularData();
    }

    public function withRegularData()
    {
        return $this
            ->with('url', 'http://url.net/file.doc')
            ->with('extension', 'doc')
            ->with('size', 1500);
    }

    public function withImageData()
    {
        return $this
            ->with('url', 'http://url.net/image.png')
            ->with('extension', 'png')
            ->with('size', 2500);
    }

    protected function getTargetClass(): string
    {
        return File::class;
    }

    protected function getTargetType(): string
    {
        return 'vo';
    }
}
