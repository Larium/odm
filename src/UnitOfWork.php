<?php

declare(strict_types = 1);

namespace Larium\ODM;

use SplObjectStorage;

class UnitOfWork
{
    private $newObjects;

    private $dirtyObjects;

    private $removedObjects;

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

    public function getIdentityMap(): IdentityMap
    {
        return $this->identityMap;
    }

    public function registerOriginal(Document $doc, object $object): void
    {
        $this->originalObjects->offsetSet($object, $doc);
    }

    public function registerClean(string $id, object $object): void
    {
        $this->identityMap->set($id, $object);
    }

    public function registerNew(object $object): void
    {
        $this->newObjects->offsetSet($object);
    }

    public function registerDirty(object $object): void
    {
        if (!$this->removedObjects->offsetExists($object)
            && !$this->dirtyObjects->offsetExists($object)
            && !$this->newObjects->offsetExists($object)
        ) {
            $this->dirtyObjects->offsetSet($object);
        }
    }

    public function registerRemoved(object $object): void
    {
        if ($this->newObjects->offsetExists($object)) {
            $this->newObjects->detach($object);

            return;
        }

        $this->dirtyObjects->offsetUnset($object);
        if (!$this->removedObjects->offsetExists($object)) {
            $this->removedObjects->offsetSet($object);
        }
    }

    public function commit(): void
    {
        $this->insertNew();
        $this->updateDirty();
        $this->deleteRemoved();
    }

    private function insertNew(): void
    {
        foreach ($this->newObjects as $obj) {
            $dataMap = $this->dm->getDataMap(get_class($obj));

            $doc = (new Hydrator($dataMap))->extract($obj);
            $this->dm->getCollection(get_class($obj))->persist($doc);
            $this->registerOriginal($doc, $obj);
            $this->registerClean($doc->getId(), $obj);
        }
        $this->newObjects->removeAll($this->newObjects);
    }

    private function updateDirty(): void
    {
        $this->computeDirty();
        foreach ($this->dirtyObjects as $obj) {
            $dataMap = $this->dm->getDataMap(get_class($obj));

            $doc = (new Hydrator($dataMap))->extract($obj);
            $this->dm->getCollection(get_class($obj))->update($doc);
        }

        $this->dirtyObjects->removeAll($this->dirtyObjects);
    }

    private function computeDirty(): void
    {
        foreach ($this->originalObjects as $obj) {
            $doc = $this->originalObjects[$obj];
            /** @var Document $doc */
            if ($this->hasChangeSet($doc, $obj)) {
                $this->registerDirty($obj);
            }
        }
    }

    private function deleteRemoved(): void
    {
        foreach ($this->removedObjects as $obj) {
            $dataMap = $this->dm->getDataMap(get_class($obj));
            $doc = (new Hydrator($dataMap))->extract($obj);
            $this->dm->getCollection(get_class($obj))->remove($doc);
        }
    }

    private function hasChangeSet(Document $doc, object $object): bool
    {
        $dataMap = $this->dm->getDataMap(get_class($object));
        $originalData = $doc->getData();

        foreach ($dataMap->getFieldMaps() as $fieldMap) {
            $fieldName = $fieldMap->getFieldName();
            $current = $fieldMap->getPropertyValue($object);
            $original = $originalData[$fieldName] ?? null;

            if ($original !== $current) {
                return true;
            }
        }

        return false;
    }
}
