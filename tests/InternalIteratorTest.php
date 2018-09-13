<?php
namespace Tests;

use function Jcstrandburg\Demeter\pick_array;
use function Jcstrandburg\Demeter\xrange;
use Jcstrandburg\Demeter\InternalIterator;
use Jcstrandburg\Demeter\InternalIteratorCache;
use PHPUnit\Framework\TestCase;

class InternalIteratorTest extends TestCase
{
    public function testLazyIteration()
    {
        $generator = 0;

        $cache = new InternalIteratorCache((function () use (&$generator) {
            foreach (range(1, 10) as $_) {
                $generator++;
                yield $generator;
            }
        })());

        $iterator = new InternalIterator($cache);

        foreach (range(1, 9) as $i) {
            $this->assertEquals(range(1, $i), pick_array($iterator, $i));
            $this->assertEquals($i + 1, $generator);
        }
    }

    /**
     * @dataProvider provider
     */
    public function testSimultaneousIteration($seq)
    {
        $cache = new InternalIteratorCache($seq);

        $iter1 = new InternalIterator($cache);
        $iter2 = new InternalIterator($cache);

        $iter1->rewind();
        $iter2->rewind();

        $this->assertEquals(1, $iter1->current());
        $this->assertEquals(1, $iter2->current());

        $iter1->next();

        $this->assertEquals(2, $iter1->current());
        $this->assertEquals(1, $iter2->current());

        $iter2->next();

        $this->assertEquals(2, $iter1->current());
        $this->assertEquals(2, $iter2->current());

        $iter1->next();
        $iter2->rewind();

        $this->assertEquals(3, $iter1->current());
        $this->assertEquals(1, $iter2->current());
    }

    public function provider()
    {
        return [
            'generator' => [(function () {yield 1;yield 2;yield 3;})()],
            'xrange' => [xrange(1, 3)],
            'array' => [[1, 2, 3]],
            'ArrayObject' => [new \ArrayObject([1, 2, 3])],
        ];
    }
}
