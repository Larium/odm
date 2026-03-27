<?php

declare(strict_types=1);

namespace Larium\ODM;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Larium\ODM\Document\User;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class UnitOfWorkTest extends TestCase
{
    use FirestoreHelperTrait;

    private Configuration $config;

    protected function setUp(): void
    {
        $loader = require __DIR__ . '/../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
        $this->config = new Configuration();
        $this->config->setDocumentsPaths([
            __DIR__ . '/Document',
        ]);
        $this->config->setClientFactory($this->createMockClientFactory());
    }

    public function testFlushCallsUpdateWhenManagedEntityDiffersFromOriginal(): void
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['update'])
            ->getMock();
        $collection->expects($this->once())->method('update')->with($this->callback(function (Document $d): bool {
            return $d->getId() === 'u1'
                && ($d->getData()['first_name'] ?? null) === 'Changed';
        }));

        $client = $this->createMock(Client::class);
        $client->method('getCollection')->with('users')->willReturn($collection);

        $config = new Configuration();
        $config->setDocumentsPaths([__DIR__ . '/Document']);
        $config->setClientFactory(new class($client) implements ClientFactory {
            public function __construct(private Client $client)
            {
            }

            public function createClient(): Client
            {
                return $this->client;
            }
        });

        $dm = new DocumentManager($config);
        $uow = $dm->getUnitOfWork();

        $user = $this->userWithId('u1', 'John', 'Doe', 1970);
        $doc = new Document('u1', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'born' => 1970,
        ]);

        $uow->registerClean('u1', $user);
        $uow->registerOriginal($doc, $user);
        $user->changeName('Changed');

        $dm->flush();
    }

    public function testFlushDoesNotUpdateWhenEntityMatchesOriginal(): void
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['update'])
            ->getMock();
        $collection->expects($this->never())->method('update');

        $client = $this->createMock(Client::class);
        $client->method('getCollection')->with('users')->willReturn($collection);

        $config = new Configuration();
        $config->setDocumentsPaths([__DIR__ . '/Document']);
        $config->setClientFactory(new class($client) implements ClientFactory {
            public function __construct(private Client $client)
            {
            }

            public function createClient(): Client
            {
                return $this->client;
            }
        });

        $dm = new DocumentManager($config);
        $uow = $dm->getUnitOfWork();

        $user = $this->userWithId('u2', 'Same', 'Value', 1970);
        $doc = new Document('u2', [
            'first_name' => 'Same',
            'last_name' => 'Value',
            'born' => 1970,
        ]);

        $uow->registerClean('u2', $user);
        $uow->registerOriginal($doc, $user);

        $dm->flush();
    }

    public function testHasChangeSetDetectsChangeWhenFieldNamesDifferFromPropertyNames(): void
    {
        $dm = new DocumentManager($this->config);
        $uow = $dm->getUnitOfWork();

        $user = $this->userWithId('id-1', 'Jane', 'Doe', 1970);
        $doc = new Document('id-1', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'born' => 1970,
        ]);

        $invoke = $this->hasChangeSetInvoker($uow);
        $this->assertTrue($invoke($doc, $user), 'first_name differs from snapshot');

        $user2 = $this->userWithId('id-2', 'Same', 'Value', 1970);
        $doc2 = new Document('id-2', [
            'first_name' => 'Same',
            'last_name' => 'Value',
            'born' => 1970,
        ]);
        $this->assertFalse($invoke($doc2, $user2), 'snapshot matches entity');
    }

    public function testHasChangeSetTreatsMissingFieldAsNull(): void
    {
        $dm = new DocumentManager($this->config);
        $uow = $dm->getUnitOfWork();

        $user = $this->userWithId('id-3', 'Only', 'First', 2000);
        $doc = new Document('id-3', [
            'last_name' => 'First',
        ]);

        $invoke = $this->hasChangeSetInvoker($uow);
        $this->assertTrue($invoke($doc, $user), 'first_name missing in snapshot but set on entity');
    }

    /**
     * @return callable(Document, object): bool
     */
    private function hasChangeSetInvoker(UnitOfWork $uow): callable
    {
        $m = new ReflectionMethod(UnitOfWork::class, 'hasChangeSet');
        $m->setAccessible(true);

        return static fn (Document $doc, object $object): bool => (bool) $m->invoke($uow, $doc, $object);
    }

    private function userWithId(string $id, string $first, string $last, int $born): User
    {
        $user = new User($first, $last, $born);
        $r = new ReflectionProperty(User::class, 'id');
        $r->setAccessible(true);
        $r->setValue($user, $id);

        return $user;
    }
}
