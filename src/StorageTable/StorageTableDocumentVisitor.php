<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use function assert;
use Larium\ODM\Document;
use Larium\ODM\DocumentVisitor;
use MicrosoftAzure\Storage\Table\Models\GetEntityResult;

class StorageTableDocumentVisitor implements DocumentVisitor
{
    public function visit(object $item): Document
    {
        assert($item instanceof GetEntityResult);
        $entity = $item->getEntity();

        return Document::load(
            sprintf("%s:%s", $entity->getPartitionKey(), $entity->getRowKey()),
            $entity->getProperties(),
            $entity->getTimestamp(),
            $entity->getTimestamp()
        );
    }
}
