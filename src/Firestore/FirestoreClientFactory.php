<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Larium\ODM\Client;

class FirestoreClientFactory
{
    public function createClient(): Client
    {
        return new FirestoreBridgeClient(new FirestoreClient());
    }
}
