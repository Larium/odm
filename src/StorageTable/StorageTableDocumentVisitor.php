<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use function assert;
use Larium\ODM\Document;
use Larium\ODM\DocumentVisitor;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\Property;

class StorageTableDocumentVisitor implements DocumentVisitor
{
    public function visit(object $item): Document
    {
        assert($item instanceof Entity);

        $props = array_map(function (Property $prop) {
            return $prop->getValue();
        }, $item->getProperties());

        $props['id'] = sprintf('%s:%s', $props['PartitionKey'], $props['RowKey']);
        unset($props['PartitionKey'], $props['RowKey'], $props['Timestamp']);

        return Document::load(
            sprintf("%s:%s", $item->getPartitionKey(), $item->getRowKey()),
            $props,
            $item->getTimestamp(),
            $item->getTimestamp()
        );
    }
}
