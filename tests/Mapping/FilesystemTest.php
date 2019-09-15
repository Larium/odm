<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping;

use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    public function testFileRead(): void
    {
        $fs = new Filesystem(__DIR__ . '/../Document');

        $classes = $fs->getClassNames();

        $class = reset($classes);

        $this->assertEquals('Larium\ODM\Document\User', $class);
    }
}
