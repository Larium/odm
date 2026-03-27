<?php

declare(strict_types=1);

namespace Larium\ODM;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Larium\ODM\Document\User;
use Larium\ODM\Mapping\MetadataRegistry;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    protected function setUp(): void
    {
        $loader = require __DIR__ . '/../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    }

    public function testHydrateSkipsMissingFields(): void
    {
        $registry = new MetadataRegistry();
        $registry->register(User::class);
        $dataMap = $registry->getDataMapForDocument(User::class);

        $hydrator = new Hydrator($dataMap);
        $doc = new Document('x', [
            'last_name' => 'Doe',
        ]);

        /** @var User $user */
        $user = $hydrator->hydrate($doc);

        $this->assertSame('x', $user->getId());
    }
}
