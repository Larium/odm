<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Larium\ODM\Document;
use Larium\ODM\FirestoreHelperTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FirestorePersisterTest extends TestCase
{
    use FirestoreHelperTrait;

    private FirestoreBridgeClient $client;

    private FirestoreClient|MockObject $firestoreClient;

    const DOC_ID = '5d7d39ed0f6bd';

    public function setUp(): void
    {
        $this->firestoreClient = $this->createFirestoreClient();
        $this->client = new FirestoreBridgeClient($this->firestoreClient);
    }

    public function testShouldPersistDocument(): void
    {
        $this->firestoreClient->collection('')->expects($this->once())
            ->method('document');

        $doc = new Document(self::DOC_ID, [
            'first' => 'John',
            'last' => 'Doe',
            'born' => 1970
        ]);

        $this->client->getCollection('users')
             ->persist($doc);
    }

    public function testShouldUpdateDocument(): void
    {
        $this->firestoreClient->collection('')->expects($this->once())
            ->method('document');

        $doc = new Document(self::DOC_ID, [
            'first' => 'John',
            'last' => 'Doe',
            'born' => 1970
        ]);
        $this->client->getCollection('users')
             ->update($doc);
    }

    public function testShouldDeleteDocument(): void
    {
        $this->firestoreClient->collection('')->document('')
            ->expects($this->once())->method('delete');

        $doc = new Document(self::DOC_ID, []);

        $this->client->getCollection('users')
             ->remove($doc);
    }
}
