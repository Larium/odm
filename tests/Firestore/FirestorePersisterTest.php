<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Larium\ODM\Document;
use PHPUnit\Framework\TestCase;

class FirestorePersisterTest extends TestCase
{
    private $client;

    const DOC_ID = '5d7d39ed0f6bd';

    public function setUp(): void
    {
        $this->client = new FirestoreBridgeClient(new FirestoreClient());
    }

    public function testShouldPersistDocument(): void
    {
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
        $doc = new Document(self::DOC_ID, []);

        $this->client->getCollection('users')
             ->remove($doc);
    }
}
