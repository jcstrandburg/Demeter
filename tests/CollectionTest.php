<?php
namespace Tests;

use function Jcstrandburg\Demeter\collect;
use function Jcstrandburg\Demeter\repeat;
use Jcstrandburg\Demeter\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testFactoryFunction()
    {
        $c = collect([1, 2, 3]);
        $this->assertInstanceOf(Collection::class, $c);

        $d = collect($c);
        $this->assertEquals($c, $d);
    }

    public function testCollection()
    {
        $collection = collect(repeat([1, 2, 3], 2));

        $this->assertEquals(6, $collection->count());
        $this->assertEquals([1, 2, 3, 1, 2, 3], $collection->toArray());
    }
}
