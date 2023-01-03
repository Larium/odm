<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Doctrine\Common\Collections\Criteria;
use Larium\ODM\Firestore\FirestoreBridgeClient;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    use FirestoreHelperTrait;

    const DOC_ID = 'TbwKbAOiojFK4j857hoq';

    /**
     * @var FirestoreBridgeClient
     */
    private $client;

    public function setUp(): void
    {
        $this->client = new FirestoreBridgeClient($this->createFirestoreClient());
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
}
