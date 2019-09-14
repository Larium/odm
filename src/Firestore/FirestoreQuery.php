<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\Query;
use Larium\ODM\QueryProxy;
use ArrayIterator;

class FirestoreQuery implements QueryProxy
{
    private $query;

    public function __construct(Query $query)
    {
         $this->query = $query;
    }

    public function getIterator(): ArrayIterator
    {
        $snapshot = $this->query->documents();

        return $snapshot->getIterator();
    }
}
