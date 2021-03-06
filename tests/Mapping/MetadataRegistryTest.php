<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Larium\ODM\Document\User;
use Larium\ODM\Mapping\Metadata\DataMap;
use PHPUnit\Framework\TestCase;

class MetadataRegistryTest extends TestCase
{
    public function setUp(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    public function testRegistry(): void
    {
        $r = new MetadataRegistry();

        $r->registerPath(__DIR__ . '/../Document');

        $dataMap = $r->getDataMapForDocument(User::class);

        $this->assertInstanceOf(DataMap::class, $dataMap);
    }
}
