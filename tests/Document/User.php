<?php

declare(strict_types = 1);

namespace Larium\ODM\Document;

use Larium\ODM\Mapping\Annotation as ODM;

/**
 * @ODM\Document(collection="users")
 */
class User
{
    /**
     * @ODM\Id(type="string")
     */
    private $id;

    /**
     * @ODM\Field(type="string")
     */
    private $first;

    /**
     * @ODM\Field(type="string")
     */
    private $last;

    /**
     * @ODM\Field(type="number")
     */
    private $born;

    public function changeName(string $name): void
    {
        $this->first = $name;
    }
}
