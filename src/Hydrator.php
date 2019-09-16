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
        $en = $this->dataMap->getDocumentClass()->newInstanceWithoutConstructor();

        $data = $doc->getData();
        foreach ($this->dataMap->getFieldMaps() as $fieldMap) {
            $fieldMap->setProperty($en, $data[$fieldMap->getFieldName()]);
        }
        $idMap = $this->dataMap->getIdMap();
        $idMap->setProperty($en, $doc->getId());

        return $en;
    }

    public function extract(object $object, Document $doc): Document
    {
        foreach ($this->dataMap->getFieldMaps() as $fieldMap) {
            $property = $fieldMap->getPropertyName();
            $doc->$property = $fieldMap->getPropertyValue($object);
        }

        return $doc;
    }
}
