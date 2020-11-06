<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Criteria;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\Filters\BinaryFilter;
use MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter;
use MicrosoftAzure\Storage\Table\Models\Filters\PropertyNameFilter;
use MicrosoftAzure\Storage\Table\Models\GetEntityOptions;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesOptions;
use MicrosoftAzure\Storage\Table\TableRestProxy;

class StorageTableExpressionVisitorTest extends TestCase
{
    private const CONNECTION_STRING = '';

    private const TABLE = 'Users';

    public function testShouldCreateQuery(): void
    {
        $c = Criteria::create()
            ->where(Criteria::expr()->eq('first', 'Andreas'))
            ->andWhere(Criteria::expr()->eq('last', 'Kollaros'));

        $expr = $c->getWhereExpression();

        $exprVisitor = new StorageTableExpressionVisitor($this->getTableRestProxy(), self::TABLE);

        $queryProxy = $exprVisitor->dispatch($expr);

        print_r($queryProxy->getIterator());
        /*
        $first = new PropertyNameFilter('first');
        $value = new ConstantFilter(EdmType::STRING, 'Andreas');
        $binary = new BinaryFilter($first, 'eq', $value);

        $options = new QueryEntitiesOptions();
        $options->setFilter($binary);

        $result = $this->getTableRestProxy()->queryEntities(self::TABLE, $options);
        */
    }

    private function getTableRestProxy(): TableRestProxy
    {
        return TableRestProxy::createTableService(self::CONNECTION_STRING);
    }
}
