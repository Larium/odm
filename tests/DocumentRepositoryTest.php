<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Larium\ODM\Document\User;
use Larium\ODM\Firestore\FirestoreClientFactory;
use PHPUnit\Framework\TestCase;

class DocumentRepositoryTest extends TestCase
{
    const CLIENT_FACTORY = FirestoreClientFactory::class;
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var DocumentManager
     */
    private $dm;


    public function setUp(): void
    {
        $loader = require __DIR__ . '/../vendor/autoload.php';

        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
        $this->config = new Configuration();
        $this->config->setDocumentsPaths([
            __DIR__ . '/Document'
        ]);
        $this->config->setClientFactory(self::CLIENT_FACTORY);
        $this->dm = new DocumentManager($this->config);
    }

    public function testShouldHydrateDocument(): void
    {
        $repo = $this->dm->getRepository(User::class);

        $doc = $repo->getDocument('TbwKbAOiojFK4j857hoq');

        $this->assertInstanceOf(User::class, $doc);

        $doc->changeName('John');

        $this->dm->flush();
    }
}
