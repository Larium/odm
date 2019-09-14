<?php

declare(strict_types = 1);

namespace Larium\ODM;

class Document
{
    private $id;

    private $name;

    private $data;

    public function __construct(string $id, string $name, array $data)
    {
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
