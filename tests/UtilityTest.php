<?php
namespace Tests;

use function Jcstrandburg\Demeter\as_traversable;
use function Jcstrandburg\Demeter\infinite;
use function Jcstrandburg\Demeter\pick_array;
use function Jcstrandburg\Demeter\repeat;
use function Jcstrandburg\Demeter\xrange;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    /**
     * @dataProvider asTraversableProvider
     */
    public function testAsTraversable(iterable $iterable)
    {
        $this->assertInstanceOf(\Traversable::class, as_traversable($iterable));
    }

    public function asTraversableProvider()
    {
        return [
            [(function () {yield 1;yield 2;})()],
            [xrange(1, 3)],
            [[1, 2, 3]],
            [new \ArrayObject([1, 2, 3])],
        ];
    }

    public function testPickToArray()
    {
        $source = [1, 2, 3];

        $this->assertEquals([], pick_array($source, 0));
        $this->assertEquals([1], pick_array($source, 1));
        $this->assertEquals([1, 2], pick_array($source, 2));
        $this->assertEquals([1, 2, 3], pick_array($source, 3));
        $this->assertEquals([1, 2, 3], pick_array($source, 4));
    }

    public function testInfinite()
    {
        $infinite = infinite(['a', 'b', 'c']);

        $this->assertEquals(['a'], $infinite->take(1)->toArray());
        $this->assertEquals(['a', 'b'], $infinite->take(2)->toArray());
        $this->assertEquals(['a', 'b', 'c'], $infinite->take(3)->toArray());
        $this->assertEquals(['a', 'b', 'c', 'a'], $infinite->take(4)->toArray());
        $this->assertEquals(['a', 'b', 'c', 'a', 'b'], $infinite->take(5)->toArray());
        $this->assertEquals(['a', 'b', 'c', 'a', 'b', 'c'], $infinite->take(6)->toArray());
    }

    public function testRepeat()
    {
        $this->assertEquals([], repeat([], 2)->toArray());
        $this->assertEquals([], repeat([1, 2], 0)->toArray());
        $this->assertEquals([1, 2], repeat([1, 2], 1)->toArray());
        $this->assertEquals([1, 2, 1, 2], repeat([1, 2], 2)->toArray());
    }
}
