<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping;

class Filesystem
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getClassNames(): array
    {
        $pattern = sprintf('%s/*.php', $this->path);

        $iterator = new \GlobIterator($pattern, \FilesystemIterator::KEY_AS_FILENAME);

        $classParser = new ClassFileParser();

        $classes = [];
        foreach ($iterator as $item) {
            $classes[] = $classParser->getClassFullNameFromFile($item->getPathName());
        }

        return $classes;
    }
}
