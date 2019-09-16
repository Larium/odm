<?php

declare(strict_types = 1);

namespace Larium\ODM;

interface ClientFactory
{
    public function createClient(): Client;
}
