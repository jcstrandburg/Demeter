<?php
namespace Tests;

use function Jcstrandburg\Demeter\pick_array;
use Jcstrandburg\Demeter\LazyRewindableIterator;
use PHPUnit\Framework\TestCase;

class LazyRewindableIteratorTest extends TestCase
{
    public function testLazyIteration()
    {
        $generator = 0;
        $iterator = new LazyRewindableIterator((function () use (&$generator) {
            foreach (range(1, 10) as $_) {
                $generator++;
                yield $generator;
            }
        })());

        foreach (range(1, 9) as $i) {
            $this->assertEquals(range(1, $i), pick_array($iterator, $i));
            $this->assertEquals($i + 1, $generator);
        }
    }
}
