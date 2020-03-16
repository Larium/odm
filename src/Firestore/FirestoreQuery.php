<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Larium\ODM\QueryProxy;
use ArrayIterator;

class FirestoreQuery implements QueryProxy
{
    private $query;

    public function __construct(CollectionReference $query)
    {
         $this->query = $query;
    }

    public function limit(int $number): QueryProxy
    {
        $this->query->limit($number);

        return $this;
    }

    public function offset(int $number): QueryProxy
    {
        $this->query->offset($number);

        return $this;
    }

    public function orderBy(string $field, int $direction): QueryProxy
    {
        $this->query->orderBy($field, $direction);

        return $this;
    }

    public function getDocument(string $id): object
    {
        $docRef = $this->query->document($id);

        $docSnap = $docRef->snapshot();
        \assert($docSnap instanceof DocumentSnapshot);

        return $docSnap;
    }

    public function getIterator(): ArrayIterator
    {
        $snapshot = $this->query->documents();

        return $snapshot->getIterator();
    }
}
