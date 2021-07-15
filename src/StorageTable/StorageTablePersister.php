<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use Larium\ODM\Document;
use Larium\ODM\Persister;
use MicrosoftAzure\Storage\Table\Models\EdmType;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\TableRestProxy;

class StorageTablePersister implements Persister
{
    private TableRestProxy $proxy;

    private string $collectionName;

    public function __construct(TableRestProxy $proxy, string $collectionName)
    {
        $this->proxy = $proxy;
        $this->collectionName = $collectionName;
    }

    public function persist(Document $document): void
    {
        $entity = new Entity();
        $entity->setPartitionKey($this->collectionName);
        $entity->setRowKey($document->getId());
        foreach ($document->getData() as $key => $value) {
            $entity->addProperty($key, EdmType::propertyType($value), $value);
            $entity->setPropertyValue($key, $value);
        }
        $this->proxy->insertEntity($this->collectionName, $entity);
    }

    public function update(Document $document): void
    {

    }

    public function remove(Document $document): void
    {

    }
}
