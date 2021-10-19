<?php

declare(strict_types = 1);

namespace Larium\ODM;

use ArrayIterator;

interface QueryProxy
{
    public function getIterator(): ArrayIterator;

    public function limit(int $number): QueryProxy;

    public function offset(int $number): QueryProxy;

    public function orderBy(string $field, int $direction): QueryProxy;

    /**
     * @throws \Larium\ODM\Exception\DocumentNotFoundException
     */
    public function getDocument(string $id): object;
}
