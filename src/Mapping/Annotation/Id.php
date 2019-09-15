<?php

declare(strict_types = 1);

namespace Larium\ODM\Mapping\Annotation;

/**
 * @Annotation
 */
final class Id
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string|null
     */
    public $name;
}
