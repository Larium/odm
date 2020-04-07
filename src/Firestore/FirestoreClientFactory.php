<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Larium\ODM\Client;
use Larium\ODM\ClientFactory;
use Google\Cloud\Firestore\FirestoreClient;

class FirestoreClientFactory implements ClientFactory
{
    public function createClient(): Client
    {
        return new FirestoreBridgeClient(new FirestoreClient());
    }
}
