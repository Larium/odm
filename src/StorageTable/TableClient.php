<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use Larium\ODM\Client;
use Larium\ODM\Collection;
use MicrosoftAzure\Storage\Table\TableRestProxy;

class TableClient implements Client
{
    private TableRestProxy $proxy;

    public function __construct(TableRestProxy $proxy)
    {
        $this->proxy = $proxy;
    }

    public function getCollection(string $collectionName): Collection
    {
        $queryProxy = new StorageTableQuery($this->proxy, $collectionName);

        return new Collection(
            $collectionName,
            new StorageTableExpressionVisitor($this->proxy, $collectionName),
            new StorageTableDocumentVisitor(),
            new StorageTablePersister($this->proxy, $collectionName),
            $queryProxy
        );
    }
}
