<?php

declare(strict_types = 1);

namespace Larium\ODM;

final class Document
{
    private $id;

    private $data;

    private $changes;

    private $createdAt;

    private $updatedAt;

    public static function load(
        string $id,
        array $data,
        \DateTimeInterface $createTime,
        \DateTimeInterface $updateTime
    ): Document {
        $doc = new Document($id, []);

        $doc->data = $data;
        $doc->createdAt = $createTime;
        $doc->updatedAt = $updateTime;

        return $doc;
    }

    public function __construct(string $id, array $data)
    {
        $this->id = $id;
        $this->data = $data;
        $this->changes = $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function exists(string $property): bool
    {
        return array_key_exists($property, $this->data);
    }

    public function __get(string $name)
    {
        if ($this->exists($name)) {
            return $this->data[$name];
        }

        throw new \InvalidArgumentException(
            sprintf("Data with name `%s` does not exist.", $name)
        );
    }

    public function __set(string $name, $value): void
    {
        if ($this->exists($name)) {
            if ($this->data[$name] !== $value) {
                $this->changes[$name] = $value;
            }
            $this->data[$name] = $value;

            return;
        }

        throw new \InvalidArgumentException(
            sprintf("Data with name `%s` does not exist.", $name)
        );
    }

    public function hasChanges(): bool
    {
        return !empty($this->changes);
    }

    public function getChanges()
    {
        return $this->changes;
    }

    public function createdAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
