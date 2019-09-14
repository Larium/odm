<?php

declare(strict_types = 1);

namespace Larium\ODM;

use ArrayIterator;

class QueryProxyStub implements QueryProxy
{
    private $query;

    public function __construct(object $query)
    {
        $this->query = $query;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator();
    }

    public function getInnerQuery(): object
    {
        return $this->query;
    }
}
