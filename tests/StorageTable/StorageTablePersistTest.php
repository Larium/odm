<?php

declare(strict_types=1);

use Larium\ODM\Document;
use PHPUnit\Framework\TestCase;
use Larium\ODM\StorageTable\TableClient;
use MicrosoftAzure\Storage\Table\TableRestProxy;

class StorageTablePersistTest extends TestCase
{
    private const CONNECTION_STRING = '';

    private $client;

    const DOC_ID = '5d7d39ed0f6bd';

    public function setUp(): void
    {
        $this->client = new TableClient($this->getTableRestProxy());
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

    private function getTableRestProxy(): TableRestProxy
    {
        return TableRestProxy::createTableService(self::CONNECTION_STRING);
    }
}
