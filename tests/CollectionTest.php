<?php

declare(strict_types = 1);

namespace Larium\ODM;

use ArrayIterator;
use DateTimeImmutable;
use Doctrine\Common\Collections\Criteria;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\Connection\ConnectionInterface;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\QuerySnapshot;
use Google\Cloud\Firestore\ValueMapper;
use Larium\ODM\Firestore\FirestoreBridgeClient;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    const DOC_ID = 'TbwKbAOiojFK4j857hoq';

    private $client;

    public function setUp(): void
    {
        $this->client = new FirestoreBridgeClient(new FirestoreClient());
    }

    public function testGetDocuments(): void
    {
        $c = Criteria::create()
            ->where(Criteria::expr()->eq('first', 'Andreas'));

        $data = $this->client->getCollection('users')->getDocuments($c);

        $this->assertNotEmpty($data);
    }

    public function testGetDocument(): void
    {
        $doc = $this->client->getCollection('users')->getDocument(self::DOC_ID);

        $this->assertInstanceOf(Document::class, $doc);
    }

    /**
     * @return FirestoreClient
     */
    private function createFirestoreClient()
    {
        $m = $this->getMockBuilder(FirestoreClient::class)
            ->setMethods(['collection', 'document'])
            ->getMock();

        $m->method('document')->willReturn($this->createMockDocument());
        $m->method('collection')->willReturn($this->createMockCollection());

        return $m;
    }

    private function createMockDocument()
    {
        $m = $this->getMockBuilder(DocumentReference::class)
            ->disableOriginalConstructor()
            ->setMethods(['snapshot', 'id'])
            ->getMock();

        $m->method('snapshot')
            ->willReturn(
                $this->createDocumentSnapshot($m)
            );
        $m->method('id')->willReturn('1');


        return $m;
    }

    private function createMockCollection()
    {
        $m = $this->getMockBuilder(CollectionReference::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIterator', 'where', 'documents', 'document'])
            ->getMock();

        $m->method('getIterator')->willReturn(new ArrayIterator());
        $m->method('where')->willReturn($m);
        $m->method('documents')->willReturn(new QuerySnapshot($m, []));
        $m->method('document')->willReturn($this->createMockDocument());

        return $m;
    }

    private function getConnection()
    {
        $m = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        return $m;
    }

    private function createDocumentSnapshot($docRef): DocumentSnapshot
    {
        return new DocumentSnapshot(
            $docRef,
            new ValueMapper($this->getConnection(), false),
            [
                'createTime' => new Timestamp(new DateTimeImmutable()),
                'updateTime' => new Timestamp(new DateTimeImmutable()),
            ],
            [
                'name' => 'test'
            ],
            true
        );
    }
}
