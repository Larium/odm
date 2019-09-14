<?php

declare(strict_types = 1);

namespace Larium\ODM;

use ArrayIterator;

interface QueryProxy
{
    public function getIterator(): ArrayIterator;
}
