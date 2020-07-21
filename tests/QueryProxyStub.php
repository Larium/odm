<?php

declare(strict_types = 1);

namespace Larium\ODM;

use ArrayIterator;

class QueryProxyStub implements QueryProxy
{
    /**
     * @var object
     */
    private $query;

    /**
     * @var object|null
     */
    private $document;

    public function __construct(object $query, object $document = null)
    {
        $this->query = $query;
        $this->document = $document;
    }

    public function getDocument(string $id): object
    {
        return $this->document;
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
