<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

use InvalidArgumentException;

final class File
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $extension;
    /**
     * @var int
     */
    private $size;

    private function __construct()
    {
    }

    public static function fromValues(string $url, string $extension, int $size)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf('URL "%s" is not valid', $url));
        }

        $file = new File();
        $file->url = $url;
        $file->extension = $extension;
        $file->size = $size;

        return $file;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}
