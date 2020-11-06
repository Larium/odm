<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use ArrayIterator;
use Larium\ODM\QueryProxy;
use MicrosoftAzure\Storage\Table\TableRestProxy;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesOptions;

class StorageTableQuery implements QueryProxy
{
    private TableRestProxy $proxy;

    private string $table;

    private QueryEntitiesOptions $options;

    public function __construct(
        TableRestProxy $proxy,
        string $table,
        QueryEntitiesOptions $options = null
    ) {
        $this->proxy = $proxy;
        $this->table = $table;
        $this->options = $options ?? new QueryEntitiesOptions();
    }

    public function getIterator(): ArrayIterator
    {
        $queryEntitiesResult = $this->proxy->queryEntities($this->table, $this->options);

        return new ArrayIterator($queryEntitiesResult->getEntities());
    }

    public function getDocument(string $id): object
    {

    }

    public function limit(int $number): QueryProxy
    {
        $this->options->setTop($number);

        return $this;
    }

    public function offset(int $number): QueryProxy
    {
        return $this;
    }

    /**
     * Ordering is not supported by Azure Storage Tables
     * @link https://docs.microsoft.com/en-us/rest/api/storageservices/querying-tables-and-entities#basic-query-syntax
     */
    public function orderBy(string $field, int $direction): QueryProxy
    {
        return $this;
    }
}
