<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping\Metadata;

class FieldMap
{
    private $fieldName;

    private $propertyName;

    private $type;

    private $dataMap;

    private $property;

    public function __construct(
        string $fieldName,
        string $propertyName,
        string $type,
        DataMap $dataMap
    ) {
        $this->fieldName = $fieldName;
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->dataMap = $dataMap;
        $this->initProperty();
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setProperty(object $object, $value): void
    {
        try {
            $this->property->setValue($object, $value);
        } catch (\ReflectionException $e) {
            throw new MetadataException(
                sprintf("Error in setting %s", $this->propertyName),
                -1,
                $e
            );
        }
    }

    private function initProperty(): void
    {
        try {
            $this->property = new \ReflectionProperty(
                $this->dataMap->getDocumentClass()->getName(),
                $this->propertyName
            );
            $this->property->setAccessible(true);
        } catch (\ReflectionException $e) {
            throw new MetadataException(
                sprintf("Unable to set up property: %s", $this->propertyName),
                -1,
                $e
            );
        }
    }
}
