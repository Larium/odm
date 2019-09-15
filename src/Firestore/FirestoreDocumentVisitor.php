<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\DocumentSnapshot;
use Larium\ODM\Document;
use Larium\ODM\DocumentVisitor;

class FirestoreDocumentVisitor implements DocumentVisitor
{
    /**
     * @param DocumentSnapshot $item
     */
    public function visit(object $item): Document
    {
        \assert($item instanceof DocumentSnapshot);

        return Document::load($item->id(), $item->data());
    }
}
