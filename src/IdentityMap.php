<?php

declare(strict_types = 1);

namespace Larium\ODM;

class IdentityMap
{
    private $data = [];

    public function contains(string $id, string $className): bool
    {
        if (!array_key_exists($className, $this->data)) {
            return false;
        }

        if (!array_key_exists($id, $this->data[$className])) {
            return false;
        }

        return true;
    }

    public function get(string $id, string $className): ?object
    {
        if (self::contains($id, $className)) {
            return $this->data[$className][$id];
        }

        return null;
    }

    public function set(string $id, object $object): void
    {
        $this->data[get_class($object)][$id] = $object;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
