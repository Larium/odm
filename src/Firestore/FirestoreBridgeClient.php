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
        $collection = $this->client->collection($collectionName);

        return new Collection(
            $collectionName,
            new FirestoreExpressionVisitor($collection),
            new FirestoreDocumentVisitor(),
            new FirestorePersister($collection),
            new FirestoreQuery($collection)
        );
    }
}
