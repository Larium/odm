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
     * @var string
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

    public function setClientFactory(string $factory): void
    {
        $this->clientFactory = $factory;
    }

    public function getClientFactory(): string
    {
        return $this->clientFactory;
    }
}
