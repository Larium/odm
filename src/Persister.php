<?php

declare(strict_types = 1);

namespace Larium\ODM;

interface Persister
{
    public function persist(Document $document): void;

    public function remove(Document $document): void;

    public function update(Document $document): void;
}
