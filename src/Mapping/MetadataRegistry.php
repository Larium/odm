<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping;

use Doctrine\Common\Annotations\AnnotationReader;
use Larium\ODM\Mapping\Annotation\Document;
use Larium\ODM\Mapping\Annotation\Field;
use Larium\ODM\Mapping\Annotation\Id;
use Larium\ODM\Mapping\Metadata\DataMap;
use Larium\ODM\Mapping\Metadata\MetadataException;

class MetadataRegistry
{
    /**
     * @var DataMap[]
     */
    private $metadata = [];

    private $reader;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    public function registerPath(string $pathName): void
    {
        $fs = new Filesystem($pathName);
        foreach ($fs->getClassNames() as $item) {
            $this->register($item);
        }
    }

    public function register(string $className): void
    {
        $refl = new \ReflectionClass($className);
        $anno = $this->reader->getClassAnnotation($refl, Document::class);

        $dataMap = new DataMap($className, $anno->collection);

        $props = $refl->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

        foreach ($props as $prop) {
            if ($id = $this->reader->getPropertyAnnotation($prop, Id::class)) {
                $dataMap->setIdMap(
                    $id->name ?: $prop->getName(),
                    $id->type,
                    $prop->getName(),
                );
                continue;
            }
            $field = $this->reader->getPropertyAnnotation($prop, Field::class);
            $dataMap->addField(
                $field->name ?: $prop->getName(),
                $field->type,
                $prop->getName()
            );
        }

        $this->metadata[$className] = $dataMap;
    }

    public function getDataMapForDocument(string $documentName): DataMap
    {
        if (array_key_exists($documentName, $this->metadata)) {
            return $this->metadata[$documentName];
        }

        throw new MetadataException(
            sprintf('Document `%s` does not exist', $documentName)
        );
    }
}
