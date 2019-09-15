<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\CollectionReference;
use Larium\ODM\Document;
use Larium\ODM\Persister;

class FirestorePersister implements Persister
{
    private $collection;

    public function __construct(CollectionReference $collection)
    {
        $this->collection = $collection;
    }

    public function persist(Document $document): void
    {
        $this->collection->document($document->getId())
             ->set($document->getData());
    }

    public function remove(Document $document): void
    {
        $this->collection->document($document->getId())
             ->delete();
    }

    public function update(Document $document): void
    {
        $this->collection->document($document->getId())
             ->set($document->getData());
    }
}
