<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Larium\ODM\Mapping\Metadata\DataMap;

class Hydrator
{
    /**
     * @var DataMap
     */
    private $dataMap;

    public function __construct(DataMap $dataMap)
    {
        $this->dataMap = $dataMap;
    }

    public function hydrate(Document $doc): object
    {
        $entity = $this->dataMap->getDocumentClass()->newInstanceWithoutConstructor();

        $data = $doc->getData();
        foreach ($this->dataMap->getFieldMaps() as $fieldMap) {
            $fieldMap->setPropertyValue($entity, $data[$fieldMap->getFieldName()]);
        }
        $idMap = $this->dataMap->getIdMap();
        $idMap->setPropertyValue($entity, $doc->getId());

        return $entity;
    }

    public function extract(object $object): Document
    {
        return new Document(
            $this->dataMap->getIdMap()->getPropertyValue($object),
            $this->dataMap->getPropertiesValues($object)
        );
    }
}
