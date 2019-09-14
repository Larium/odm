<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Larium\ODM\Client;
use Larium\ODM\Collection;

class FirestoreBridgeClient implements Client
{
    private $client;

    public function __construct(FirestoreClient $client)
    {
        $this->client = $client;
    }

    public function getCollection(string $collectionName): Collection
    {
        return new Collection(
            $collectionName,
            new FirestoreExpressionVisitor($this->client->collection($collectionName)),
            new FirestoreDocumentVisitor()
        );
    }
}
