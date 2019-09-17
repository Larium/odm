<?php

declare(strict_types = 1);

namespace Larium\ODM;

use SplObjectStorage;

class UnitOfWork
{
    private $newObjects = [];

    private $dirtyObjects = [];

    private $removedObjects = [];

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * @var SplObjectStorage
     */
    private $originalObjects;

    public function __construct(DocumentManager $dm)
    {
        $this->newObjects = new SplObjectStorage();
        $this->dirtyObjects = new SplObjectStorage();
        $this->removedObjects = new SplObjectStorage();
        $this->identityMap = new IdentityMap();
        $this->originalObjects = new SplObjectStorage();
        $this->dm = $dm;
    }

    public function registerOriginal(Document $doc, object $object): void
    {
        $this->originalObjects->attach($object, $doc);
    }

    public function registerClean(string $id, object $object): void
    {
        $this->identityMap->set($id, $object);
    }

    public function registerNew(object $object): void
    {
        $this->newObjects->attach($object);
    }

    public function registerDirty(object $object): void
    {
        if (!$this->removedObjects->contains($object)
            && !$this->dirtyObjects->contains($object)
            && !$this->newObjects->contains($object)
        ) {
            $this->dirtyObjects->attach($object);
        }
    }

    public function registerRemoved(object $object): void
    {
        if ($this->newObjects->contains($object)) {
            $this->newObjects->detach($object);

            return;
        }

        $this->dirtyObjects->detach($object);
        if (!$this->removedObjects->contains($object)) {
            $this->removedObjects->attach($object);
        }
    }

    public function commit(): void
    {
        $this->insertNew();
        $this->computeDirty();
        $this->updateDirty();
        $this->deleteRemoved();
    }

    private function insertNew(): void
    {
    }

    private function computeDirty(): void
    {
        foreach ($this->originalObjects as $obj) {
            $doc = $this->originalObjects[$obj];
            if ($this->hasChangeSet($doc, $obj)) {
                $this->registerDirty($obj);
            }
        }
        print_r($this->dirtyObjects);
    }

    private function updateDirty(): void
    {
    }

    private function deleteRemoved(): void
    {
    }

    private function hasChangeSet(Document $doc, object $object): bool
    {
        $dataMap = $this->dm->getDataMap(get_class($object));
        $fieldMaps = $dataMap->getPropertiesValues($object);
        $data = $doc->getData();

        $common = array_intersect_key($data, $fieldMaps);
        $diff = array_diff($fieldMaps, $common);

        return !empty($diff);
    }
}
