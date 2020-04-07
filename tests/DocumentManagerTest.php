<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Larium\ODM\Document\User;
use PHPUnit\Framework\TestCase;

class DocumentManagerTest extends TestCase
{
    use FirestoreHelperTrait;

    /**
     * @var Configuration
     */
    private $config;

    public function setUp(): void
    {
        $loader = require __DIR__ . '/../vendor/autoload.php';

        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
        $this->config = new Configuration();
        $this->config->setDocumentsPaths([
            __DIR__ . '/Document'
        ]);
        $this->config->setClientFactory($this->createMockClientFactory());
    }

    public function testShouldCreateRepository(): void
    {
        $dm = new DocumentManager($this->config);

        $repo = $dm->getRepository(User::class);

        $this->assertInstanceOf(DocumentRepository::class, $repo);
    }

    public function testShouldInsertNewEntries(): void
    {
        $dm = new DocumentManager($this->config);

        $user = new User('John', 'Doe', 1970);

        $dm->persist($user);
        $dm->flush();

        $this->assertTrue($dm->getUnitOfWork()->getIdentityMap()->contains($user->getId(), User::class));
    }
}
