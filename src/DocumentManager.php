<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Larium\ODM\Mapping\MetadataRegistry;
use Larium\ODM\Mapping\Metadata\DataMap;

class DocumentManager
{
    /**
     * @var MetadataRegistry
     */
    private $metadataRegistry;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    public function __construct(Configuration $config)
    {
        $this->metadataRegistry = new MetadataRegistry();
        foreach ($config->getDocumentsPaths() as $path) {
            $this->metadataRegistry->registerPath($path);
        }
        $this->client = $config->getClientFactory()->createClient();

        $this->config = $config;
        $this->unitOfWork = new UnitOfWork($this);
    }

    public function getUnitOfWork(): UnitOfWork
    {
        return $this->unitOfWork;
    }

    public function getRepository(string $className): DocumentRepository
    {
        $dataMap = $this->metadataRegistry->getDataMapForDocument($className);

        return new DocumentRepository($this, $dataMap);
    }

    public function getCollection(string $className): Collection
    {
        $dataMap = $this->metadataRegistry->getDataMapForDocument($className);

        return  $this->client->getCollection($dataMap->getCollectionName());
    }

    public function getDataMap(string $className): DataMap
    {
        return $this->metadataRegistry->getDataMapForDocument($className);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    public function persist(object $object): void
    {
        $this->unitOfWork->registerNew($object);
    }

    public function flush(): void
    {
        $this->unitOfWork->commit();
    }

    public function remove(object $object): void
    {
        $this->unitOfWork->registerRemoved($object);
    }
}
