<?php

declare(strict_types = 1);

namespace Larium\ODM;

use ArrayIterator;
use DateTimeImmutable;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\Connection\ConnectionInterface;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\QuerySnapshot;
use Google\Cloud\Firestore\ValueMapper;
use Larium\ODM\Firestore\FirestoreBridgeClient;

trait FirestoreHelperTrait
{

    /**
     * @return FirestoreClient
     */
    protected function createFirestoreClient()
    {
        $m = $this->getMockBuilder(FirestoreClient::class)
            ->setMethods(['collection', 'document'])
            ->getMock();

        $m->method('document')->willReturn($this->createMockDocument());
        $m->method('collection')->willReturn($this->createMockCollection());

        return $m;
    }

    /**
     * @return DocumentReference
     */
    protected function createMockDocument()
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

    /**
     * @return CollectionReference
     */
    protected function createMockCollection(string $name = 'test')
    {
        $conn = $this->getConnection();
        $vm = $this->createValueMapper();

        $m = $this->getMockBuilder(CollectionReference::class)
            ->setConstructorArgs([$conn, $vm, $name . '/databases/' . $name])
            ->setMethods(['getIterator', 'where', 'documents'])
            ->getMock();

        $m->method('getIterator')->willReturn(new ArrayIterator());
        $m->method('where')->willReturn($m);
        $m->method('documents')->willReturn(
            $this->createQuerySnapshot($m)
        );

        return $m;
    }

    /**
     * @param CollectionReference $c
     */
    protected function createQuerySnapshot($c): QuerySnapshot
    {
        $docRef = $this->createMockDocument();

        return new QuerySnapshot($c, [$docRef->snapshot()]);
    }

    /**
     * @return ConnectionInterface
     */
    protected function getConnection()
    {
        $m = $this->getMockBuilder(ConnectionInterface::class)
            ->setMethods(['commit', 'batchGetDocuments', 'beginTransaction', 'listCollectionIds', 'listDocuments', 'rollback', 'runQuery'])
            ->getMock();

        $m->method('commit')
            ->willReturn([]);

        return $m;
    }

    protected function createDocumentSnapshot($docRef): DocumentSnapshot
    {
        return new DocumentSnapshot(
            $docRef,
            $this->createValueMapper(),
            [
                'createTime' => new Timestamp(new DateTimeImmutable()),
                'updateTime' => new Timestamp(new DateTimeImmutable()),
            ],
            [
                'first' => 'Andreas',
                'last' => 'Kollaros',
                'born' => 1900
            ],
            true
        );
    }

    protected function createValueMapper(): ValueMapper
    {
        return new ValueMapper($this->getConnection(), false);
    }

    /**
     * @return ClientFactory
     */
    protected function createMockClientFactory()
    {
        $m = $this->getMockBuilder(ClientFactory::class)
            ->setMethods(['createClient'])
            ->getMock();

        $m->method('createClient')->willReturn(
            new FirestoreBridgeClient($this->createFirestoreClient())
        );
        return $m;
    }
}
