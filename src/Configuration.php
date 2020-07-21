<?php

declare(strict_types = 1);

namespace Larium\ODM;

class Configuration
{
    /**
     * @var string[]
     */
    private $paths = [];

    /**
     * @var ClientFactory
     */
    private $clientFactory;


    public function setDocumentsPaths(array $paths)
    {
        $this->paths = $paths;
    }

    public function getDocumentsPaths(): array
    {
        return $this->paths;
    }

    public function setClientFactory(ClientFactory $factory): void
    {
        $this->clientFactory = $factory;
    }

    public function getClientFactory(): ClientFactory
    {
        return $this->clientFactory;
    }
}
