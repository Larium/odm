<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use ArrayIterator;
use Larium\ODM\QueryProxy;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\Query;

class FirestoreQuery implements QueryProxy
{
    private $query;

    public function __construct(Query $query)
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
        if (!$this->query instanceof CollectionReference) {
            throw new \RuntimeException(
                'getDocument by id requires a collection reference; use the query iterator for filtered results.'
            );
        }

        return $this->query->document($id)->snapshot();
    }

    public function getIterator(): ArrayIterator
    {
        $snapshot = $this->query->documents();

        return $snapshot->getIterator();
    }
}
