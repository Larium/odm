<?php

declare(strict_types=1);

use Larium\ODM\Document;
use PHPUnit\Framework\TestCase;
use Larium\ODM\StorageTable\TableClient;
use MicrosoftAzure\Storage\Table\Internal\JsonODataReaderWriter;
use MicrosoftAzure\Storage\Table\Models\InsertEntityResult;
use MicrosoftAzure\Storage\Table\TableRestProxy;
use PHPUnit\Framework\MockObject\MockObject;

class StorageTablePersistTest extends TestCase
{
    private const CONNECTION_STRING = '';

    /**
     * @var TableClient
     */
    private $client;

    const DOC_ID = '5d7d39ed0f6bd';

    public function setUp(): void
    {
        $this->client = new TableClient($this->getMockTableRestProxy());
    }

    public function testShouldPersistDocument(): void
    {
        $doc = new Document(self::DOC_ID, [
            'first' => 'John',
            'last' => 'Doe',
            'born' => 1972
        ]);

        $this->client->getCollection('users')
             ->persist($doc);
    }

    private function getTableRestProxy(): TableRestProxy
    {
        return TableRestProxy::createTableService(self::CONNECTION_STRING);
    }

    /**
     * @return MockObject|TableRestProxy
     */
    private function getMockTableRestProxy()
    {
        $mock = $this->getMockBuilder(TableRestProxy::class)
            ->disableOriginalConstructor()
            ->setMethods(['insertEntity'])
            ->getMock();

        $mock->expects($this->once())
            ->method('insertEntity')
            ->willReturn($this->getSuccessInsertEntity());

        return $mock;
    }

    private function getSuccessInsertEntity(): InsertEntityResult
    {
        $body = '{"odata.metadata":"https://tabledatabase.table.core.windows.net/$metadata#users/@Element","odata.etag":"W/\"datetime\'2021-07-29T18%3A58%3A15.2572092Z\'\"","PartitionKey":"users","RowKey":"6102f87a0c314","Timestamp":"2021-07-29T18:58:15.2572092Z","first":"John","last":"Doe","born":1972}';
        $headers = [
            'x-ms-request-id' => '57643562-9002-006b-5aab-84a2ed000000',
            'x-ms-version' => '2016-05-31',
            'date' => 'Thu, 29 Jul 2021 18:58:14 GMT',
            'x-ms-continuation-location-mode' => 'PrimaryOnly',
            'content-type' => 'application/json;odata=minimalmetadata;streaming=true;charset=utf-8'
        ];

        return InsertEntityResult::create(
            \GuzzleHttp\Psr7\Utils::streamFor($body),
            $headers,
            new JsonODataReaderWriter()
        );
    }
}
