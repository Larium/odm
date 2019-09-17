<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping\Metadata;

class DataMap
{
    private $documentClass;

    private $collectionName;

    private $fieldMaps = [];

    private $idMap;

    public function __construct(string $documentClass, string $collectionName)
    {
        $this->documentClass = new \ReflectionClass($documentClass);
        $this->collectionName = $collectionName;
    }

    public function getDocumentClass(): \ReflectionClass
    {
        return $this->documentClass;
    }

    public function getCollectionName(): string
    {
        return $this->collectionName;
    }

    /**
     * @return array FieldMap[]
     */
    public function getFieldMaps(): array
    {
        return $this->fieldMaps;
    }

    public function setIdMap(
        string $fieldName,
        string $type,
        string $propertyName
    ) {
        $this->idMap = new FieldMap(
            $fieldName,
            $propertyName,
            $type,
            $this
        );
    }

    public function getIdMap(): FieldMap
    {
        return $this->idMap;
    }

    public function addField(
        string $fieldName,
        string $type,
        string $propertyName
    ): void {
        $this->fieldMaps[] = new FieldMap(
            $fieldName,
            $propertyName,
            $type,
            $this
        );
    }

    public function getPropertiesValues(object $object): array
    {
        $data = [];
        foreach ($this->fieldMaps as $fieldMap) {
            $data[$fieldMap->getPropertyName()] = $fieldMap->getPropertyValue($object);
        }

        return $data;
    }

    public function getFieldForProperty(string $propertyName): string
    {
        foreach ($this->fieldMaps as $fieldMap) {
            if ($fieldMap->getPropertyName() == $propertyName) {
                return $fieldMap->getFieldName();
            }
        }

        throw new MetadataException(
            sprintf("Unable to find field for `%s`", $propertyName)
        );
    }
}
