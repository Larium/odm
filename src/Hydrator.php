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

    public function hydrate(Document $doc, ?object $object = null): object
    {
        $en = $object ?? $this->dataMap->getDocumentClass()->newInstanceWithoutConstructor();

        $data = $doc->getData();
        foreach ($this->dataMap->getFieldMaps() as $fieldMap) {
            $fieldName = $fieldMap->getFieldName();
            if (!array_key_exists($fieldName, $data)) {
                continue;
            }
            $fieldMap->setProperty($en, $data[$fieldName]);
        }
        $idMap = $this->dataMap->getIdMap();
        $idMap->setProperty($en, $doc->getId());

        return $en;
    }

    public function extract(object $object): Document
    {
        $idValue = $this->dataMap->getIdMap()->getPropertyValue($object);
        
        // Map property values to field names (database format)
        $fieldData = [];
        foreach ($this->dataMap->getFieldMaps() as $fieldMap) {
            $fieldName = $fieldMap->getFieldName();
            $propertyValue = $fieldMap->getPropertyValue($object);
            $fieldData[$fieldName] = $propertyValue;
        }
        
        return new Document($idValue, $fieldData);
    }
}
