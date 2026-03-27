<?php

declare(strict_types=1);

namespace Larium\ODM;

use PHPUnit\Framework\TestCase;

class IdentityMapTest extends TestCase
{
    public function testSetGetContainsUseConsistentClassBucket(): void
    {
        $map = new IdentityMap();
        $o = new \stdClass();

        $map->set('a1', $o);

        $this->assertTrue($map->contains('a1', \stdClass::class));
        $this->assertSame($o, $map->get('a1', \stdClass::class));
    }
}
