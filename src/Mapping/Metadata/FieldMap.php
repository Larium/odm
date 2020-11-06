<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping\Metadata;

use ReflectionException;
use ReflectionProperty;

class FieldMap
{
    /**
     * The name of the field in database.
     *
     * @var string
     */
    private $fieldName;

    /**
     * The name of the property of the class object.
     *
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $documentClass;

    /**
     * @var ReflectionProperty
     */
    private $property;

    public function __construct(
        string $fieldName,
        string $propertyName,
        string $type,
        string $documentClass
    ) {
        $this->fieldName = $fieldName;
        $this->propertyName = $propertyName;
        $this->type = $type;
        $this->documentClass = $documentClass;
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

    /**
     * @param object $object The source object to hydrate.
     * @param mixed $value The value to set for the property of the object.
     */
    public function setPropertyValue(object $object, $value): void
    {
        try {
            $this->setValue($object, $value);
        } catch (ReflectionException $e) {
            throw new MetadataException(
                sprintf("Error in setting %s", $this->propertyName),
                -1,
                $e
            );
        }
    }

    public function getPropertyValue(object $object)
    {
        return $this->property->getValue($object);
    }

    private function initProperty(): void
    {
        try {
            $this->property = new ReflectionProperty(
                $this->documentClass,
                $this->propertyName
            );
            $this->property->setAccessible(true);
        } catch (ReflectionException $e) {
            throw new MetadataException(
                sprintf("Unable to set up property: %s", $this->propertyName),
                -1,
                $e
            );
        }
    }

    private function setValue(object $object, $value): void
    {
        $this->property->setValue($object, $value);
    }
}
