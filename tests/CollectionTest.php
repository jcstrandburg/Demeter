<?php
namespace Tests;

use function Jcstrandburg\Demeter\collect;
use function Jcstrandburg\Demeter\repeat;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testCollection()
    {
        $collection = collect(repeat([1, 2, 3], 2));

        $this->assertEquals(6, $collection->count());
        $this->assertEquals([1, 2, 3, 1, 2, 3], $collection->toArray());
    }
}
