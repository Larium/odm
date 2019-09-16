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

    public function registerOriginal(Document $doc, string $className): void
    {
        $this->originalObjects->attach($doc, $className);
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
        $ids = $this->identityMap->getData();

        foreach ($this->originalObjects as $doc) {
            $collection = $this->originalObjects[$doc];
            $objs = $ids[$collection];
            foreach ($objs as $id => $obj) {
                if ($id === $doc->getId()) {
                    if ($this->hasChangeSet($doc, $obj)) {
                        $this->registerDirty($obj);
                    }
                }
            }
        }
    }

    private function updateDirty(): void
    {
    }

    private function deleteRemoved(): void
    {
    }

    private function hasChangeSet(Document $doc, object $object): bool
    {
        $datamap = $this->dm->getDataMap(get_class($object));
        $hydrator = new Hydrator($datamap);

        return $hydrator->extract($object, $doc)->hasChanges();
    }
}
