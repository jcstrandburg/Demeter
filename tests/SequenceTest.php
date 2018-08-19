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

    public function testSkipWhile()
    {
        $this->assertEquals(
            [3, 2, 2, 2],
            sequence([1, 1, 1, 3, 2, 2, 2])->skipWhile(function ($x) {return $x < 3;})->toArray());

        $this->assertEquals(
            [],
            sequence([2, 3, 4])->skipWhile(function ($x) {return $x < 5;})->toArray());

        $this->assertEquals(
            [2, 3, 4],
            sequence([2, 3, 4])->skipWhile(function ($x) {return $x < 2;})->toArray());
    }

    public function testTake()
    {
        $this->assertEquals([1, 2, 3], sequence(self::SOURCE)->take(3)->toArray());
        $this->assertEquals([], sequence(self::SOURCE)->take(0)->toArray());
    }

    public function testTakeWhile()
    {
        $this->assertEquals(
            [1, 1, 1],
            sequence([1, 1, 1, 2, 1, 1, 1])->takeWhile(function ($x) {return $x < 2;})->toArray());

        $this->assertEquals(
            [],
            sequence([2, 3, 4])->takeWhile(function ($x) {return $x < 2;})->toArray());

        $this->assertEquals(
            [2, 3, 4],
            sequence([2, 3, 4])->takeWhile(function ($x) {return $x < 5;})->toArray());
    }

    public function testSlice()
    {
        $this->assertEquals([7, 8, 9], sequence(self::SOURCE)->slice(6, 3)->toArray());
        $this->assertEquals(self::SOURCE, sequence(self::SOURCE)->slice(0, 10)->toArray());
    }

    public function testFold()
    {
        $add = function ($a, $b) {return $a + $b;};
        $mul = function ($a, $b) {return $a * $b;};

        $this->assertEquals(0, sequence([])->fold(0, $mul));
        $this->assertEquals(1, sequence([])->fold(1, $mul));
        $this->assertEquals(24, sequence([2, 3, 4])->fold(1, $mul));

        $this->assertEquals(10, sequence([1, 2, 3, 4])->fold(0, $add));
    }

    public function testAll()
    {
        $odds = sequence([1, 3, 5, 7]);
        $evens = sequence([2, 4, 6, 8]);
        $mixed = sequence([1, 2, 3, 4]);

        $is_odd = function ($x) {return $x % 2 == 1;};
        $is_even = function ($x) {return $x % 2 == 0;};

        $this->assertTrue($odds->all($is_odd));
        $this->assertFalse($odds->all($is_even));

        $this->assertFalse($evens->all($is_odd));
        $this->assertTrue($evens->all($is_even));

        $this->assertFalse($mixed->all($is_odd));
        $this->assertFalse($mixed->all($is_even));
    }

    public function testAny()
    {
        $odds = sequence([1, 3, 5, 7]);
        $evens = sequence([2, 4, 6, 8]);
        $mixed = sequence([1, 2, 3, 4]);

        $is_odd = function ($x) {return $x % 2 == 1;};
        $is_even = function ($x) {return $x % 2 == 0;};

        $this->assertTrue($odds->any($is_odd));
        $this->assertFalse($odds->any($is_even));

        $this->assertFalse($evens->any($is_odd));
        $this->assertTrue($evens->any($is_even));

        $this->assertTrue($mixed->any($is_odd));
        $this->assertTrue($mixed->any($is_even));
    }

    public function testFirst()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(1, $source->first());
        $this->assertEquals(11, $source->first(function ($x) {return $x > 10;}));

        $this->expectException(\LogicException::class);
        $source->first(function ($x) {return $x > 12;});
    }

    public function testFirstOrNull()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(1, $source->firstOrNull());
        $this->assertEquals(11, $source->firstOrNull(function ($x) {return $x > 10;}));
        $this->assertEquals(null, $source->firstOrNull(function ($x) {return $x > 12;}));
    }

    public function testLast()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(12, $source->last());
        $this->assertEquals(10, $source->last(function ($x) {return $x < 11;}));

        $this->expectException(\LogicException::class);
        $source->last(function ($x) {return $x > 12;});
    }

    public function testLastOrNull()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(12, $source->last());
        $this->assertEquals(10, $source->last(function ($x) {return $x < 11;}));

        $this->expectException(\LogicException::class);
        $this->assertEquals(null, $source->last(function ($x) {return $x > 12;}));
    }

    public function testSingle()
    {
        $this->assertEquals(1, sequence([1])->single());
        $this->assertEquals(1, sequence([1])->singleOrNull());

        $this->assertEquals(2, sequence([1, 2, 3])->single(function ($x) {return $x % 2 == 0;}));
        $this->assertEquals(2, sequence([1, 2, 3])->singleOrNull(function ($x) {return $x % 2 == 0;}));

        $this->assertEquals(null, sequence([1, 2, 3])->singleOrNull(function ($x) {return $x > 3;}));

        $this->expectException(\LogicException::class);
        sequence([1, 2, 3])->single(function ($x) {return $x > 3;});
    }

    public function testEmpty()
    {
        $this->assertEquals([], sequence([])->toArray());
    }
}
