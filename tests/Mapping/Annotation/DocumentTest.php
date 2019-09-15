<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Larium\ODM\Document\User;
use Larium\ODM\Mapping\Metadata\DataMap;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    private $reader;

    public function setUp(): void
    {
        $loader = require __DIR__ . '/../../../vendor/autoload.php';

        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
        $this->reader = new AnnotationReader();
    }

    public function testShouldReadAnnotaionDocument(): void
    {
        $refl = new \ReflectionClass(User::class);
        $anno = $this->reader->getClassAnnotation($refl, Document::class);

        $this->assertEquals('users', $anno->collection);
    }
}
