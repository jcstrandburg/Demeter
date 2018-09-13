<?php
namespace Tests;

use function Jcstrandburg\Demeter\xrange;
use Jcstrandburg\Demeter\InternalIteratorCache;
use PHPUnit\Framework\TestCase;

class InternalIteratorCacheTest extends TestCase
{
    public function testWhatever()
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider sequenceProvider
     */
    public function testGetIndex($seq)
    {
        $cache = new InternalIteratorCache($seq);

        $this->assertTrue($cache->hasIndex(0));
        $this->assertEquals(1, $cache->getIndex(0));

        $this->assertTrue($cache->hasIndex(1));
        $this->assertEquals(2, $cache->getIndex(1));

        $this->assertTrue($cache->hasIndex(2));
        $this->assertEquals(3, $cache->getIndex(2));
    }

    /**
     * @dataProvider sequenceProvider
     */
    public function testGetNegativeIndexThrows($seq)
    {
        $cache = new InternalIteratorCache($seq);
        $this->assertFalse($cache->hasIndex(-2));
        $this->expectException(\OutOfBoundsException::class);
        $cache->getIndex(-2);
    }

    /**
     * @dataProvider sequenceProvider
     */
    public function testGetOutOfBoundsIndexThrows($seq)
    {
        $cache = new InternalIteratorCache($seq);
        $this->assertFalse($cache->hasIndex(4));
        $this->expectException(\OutOfBoundsException::class);
        $cache->getIndex(4);
    }

    public function sequenceProvider()
    {
        return [
            'generator' => [(function () {yield 1;yield 2;yield 3;})()],
            'xrange' => [xrange(1, 3)],
            'array' => [[1, 2, 3]],
            'ArrayObject' => [new \ArrayObject([1, 2, 3])],
        ];
    }
}
