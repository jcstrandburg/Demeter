<?php
namespace Tests;

use function Jcstrandburg\Demeter\sequence;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    const SOURCE = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    public function testMap()
    {
        $c = sequence(self::SOURCE)->map(function ($x) {return $x * 2;})->toArray();
        $this->assertEquals([2, 4, 6, 8, 10, 12, 14, 16, 18, 20], $c);
    }

    public function testFlapMap()
    {
        $this->assertEquals(
            [2, 4, 6, 8, 10],
            sequence([[1, 2], [3], [4, 5]])
                ->flatMap(function ($x) {
                    return array_map(function ($y) {return $y * 2;}, $x);
                })
                ->toArray());
    }

    public function testFlapMapWithDefault()
    {
        $this->assertEquals(
            ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
            sequence([
                ['a', 'b', 'c'],
                ['d', 'e'],
                ['f', 'g', 'h']])
                ->flatMap()
                ->toArray());
    }

    public function testFilter()
    {
        $c = sequence(self::SOURCE)->filter(function ($x) {return $x % 2 == 0;})->toArray();
        $this->assertEquals([2, 4, 6, 8, 10], $c);
    }

    public function testAppend()
    {
        $c = sequence(['a'])->append('b')->append('c')->toArray();
        $this->assertEquals(['a', 'b', 'c'], $c);
    }

    public function testConcat()
    {
        $c = sequence([])->concat([3, 2, 1])->concat([6, 5, 4])->toArray();
        $this->assertEquals([3, 2, 1, 6, 5, 4], $c);
    }

    public function testChaining()
    {
        $c = sequence([])
            ->concat([1, 2, 3, 4])
            ->map(function ($x) {return $x * 3;})
            ->filter(function ($y) {return $y > 3 && $y <= 9;})
            ->append(10)
            ->toArray();

        $this->assertEquals([6, 9, 10], $c);
    }

    public function testMultipleEnumeration()
    {
        $s = sequence([1, 2, 3, 4, 5]);

        $evens = $s->filter(function ($x) {return $x % 2 == 0;})->toArray();
        $odds = $s->filter(function ($x) {return $x % 2 == 1;})->toArray();

        $this->assertEquals([2, 4], $evens);
        $this->assertEquals([1, 3, 5], $odds);
    }

    public function testSkip()
    {
        $this->assertEquals([6, 7, 8, 9, 10], sequence(self::SOURCE)->skip(5)->toArray());
        $this->assertEquals(self::SOURCE, sequence(self::SOURCE)->skip(0)->toArray());
    }

    public function testTake()
    {
        $this->assertEquals([1, 2, 3], sequence(self::SOURCE)->take(3)->toArray());
        $this->assertEquals([], sequence(self::SOURCE)->take(0)->toArray());
    }

    public function testLimit()
    {
        $this->assertEquals([7, 8, 9], sequence(self::SOURCE)->limit(6, 3)->toArray());
        $this->assertEquals(self::SOURCE, sequence(self::SOURCE)->limit(0, 10)->toArray());
    }

    public function testEmpty()
    {
        $this->assertEquals([], sequence([])->toArray());
    }
}
