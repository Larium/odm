<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Doctrine\Common\Collections\Criteria;
use Google\Cloud\Firestore\FirestoreClient;
use Larium\ODM\Firestore\FirestoreBridgeClient;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
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

        print_r($data);
    }
}
