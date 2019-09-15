<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Larium\ODM\Mapping\Metadata\DataMap;
use PHPUnit\Framework\TestCase;

class MetadataRegistryTest extends TestCase
{
    public function setUp(): void
    {
        $loader = require __DIR__ . '/../../vendor/autoload.php';

        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    }

    public function testRegistry(): void
    {
        $r = new MetadataRegistry();

        $r->registerPath(__DIR__ . '/../Document');

        $dataMap = $r->getDataMapForCollection('users');

        $this->assertInstanceOf(DataMap::class, $dataMap);
    }
}
