<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Criteria;
use Larium\ODM\Document;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\Filters\BinaryFilter;
use MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter;
use MicrosoftAzure\Storage\Table\Models\Filters\PropertyNameFilter;
use MicrosoftAzure\Storage\Table\Models\GetEntityOptions;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesOptions;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesResult;
use MicrosoftAzure\Storage\Table\TableRestProxy;
use PHPUnit\Framework\MockObject\MockObject;

class StorageTableExpressionVisitorTest extends TestCase
{
    private const CONNECTION_STRING = 'DefaultEndpointsProtocol=https;AccountName=tabledatabase;AccountKey=gpexRt6+AJCbu1eKJmpqTGt5Y0TS/v2CwRXTzbWFuvYRalLxhyKh3Q9aHnAoEzMmgcSpfKFXR94V4JQhO6lNbA==;EndpointSuffix=core.windows.net';

    private const TABLE = 'Users';

    public function testShouldCreateQuery(): void
    {
        $c = Criteria::create()
            ->where(Criteria::expr()->eq('first', 'John'))
            ->andWhere(Criteria::expr()->eq('last', 'Doe'));

        $expr = $c->getWhereExpression();

        $exprVisitor = new StorageTableExpressionVisitor($this->getTableRestProxy(), self::TABLE);

        $queryProxy = $exprVisitor->dispatch($expr);

        $queryProxy->getIterator();

        /*
        $first = new PropertyNameFilter('first');
        $value = new ConstantFilter(EdmType::STRING, 'Andreas');
        $binary = new BinaryFilter($first, 'eq', $value);

        $options = new QueryEntitiesOptions();
        $options->setFilter($binary);

        $result = $this->getTableRestProxy()->queryEntities(self::TABLE, $options);
        */
    }

    public function testShouldGetDocumentById(): void
    {
        $client = new TableClient($this->getTableRestProxy());

        $c = $client->getCollection('Users');

        $doc = $c->getDocument('users:5d7d39ed0f6bd');

        $this->assertInstanceOf(Document::class, $doc);
    }

    /**
     * @return MockObject|TableRestProxy
     */
    private function getTableRestProxy()
    {
        $mock = $this->getMockBuilder(TableRestProxy::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['queryEntities'])
            ->getMock();

        $mock->expects($this->any())->method('queryEntities')
            ->willReturn($this->getQueryEntitiesResult());
        return $mock;
        return TableRestProxy::createTableService(self::CONNECTION_STRING);
    }

    private function getQueryEntitiesResult()
    {
        return QueryEntitiesResult::create([], []);
    }
}
