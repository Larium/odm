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

    public function getDocument(string $id): object
    {
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator();
    }

    public function getInnerQuery(): object
    {
        return $this->query;
    }

    public function limit(int $number): QueryProxy
    {
        return $this;
    }

    public function offset(int $number): QueryProxy
    {
        return $this;
    }

    public function orderBy(string $field, int $direction): QueryProxy
    {
        return $this;
    }
}
