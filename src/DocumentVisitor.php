<?php

declare(strict_types = 1);

namespace Larium\ODM;

interface DocumentVisitor
{
    public function visit(object $item): Document;
}
