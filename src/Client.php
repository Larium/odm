<?php

declare(strict_types = 1);

namespace Larium\ODM;

interface Client
{
    public function getCollection(string $collectionName): Collection;
}
