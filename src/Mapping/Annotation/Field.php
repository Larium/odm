<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping\Annotation;

/**
 * @Annotation
 */
class Field
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string | null
     */
    public $name;
}
