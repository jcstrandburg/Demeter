<?php
namespace Tests;

use function Jcstrandburg\Demeter\sequence;
use function Jcstrandburg\Demeter\xrange;
use Jcstrandburg\Demeter\Lambda;
use Jcstrandburg\Demeter\LazySequence;
use Jcstrandburg\Demeter\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    const SOURCE = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    public function testFactoryFunction()
    {
        $s = sequence([1, 2, 3]);
        $this->assertInstanceOf(Sequence::class, $s);

        $t = sequence($s);
        $this->assertEquals($s, $t);
    }

    public function testMap()
    {
        $c = sequence(self::SOURCE)->map(Lambda::multiplyBy(2))->toArray();
        $this->assertEquals([2, 4, 6, 8, 10, 12, 14, 16, 18, 20], $c);
    }

    public function testFlapMap()
    {
        $this->assertEquals(
            [2, 4, 6, 8, 10],
            sequence([[1, 2], [3], [4, 5]])
                ->flatMap(function ($x) {
                    return array_map(Lambda::multiplyBy(2), $x);
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
        $c = sequence(self::SOURCE)->filter(Lambda::isEven())->toArray();
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
            ->map(Lambda::multiplyBy(3))
            ->filter(function ($y) {return $y > 3 && $y <= 9;})
            ->append(10)
            ->toArray();

        $this->assertEquals([6, 9, 10], $c);
    }

    public function testMultipleEnumeration()
    {
        $s = sequence([1, 2, 3, 4, 5]);

        $evens = $s->filter(Lambda::isEven())->toArray();
        $odds = $s->filter(Lambda::isOdd())->toArray();

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
            sequence([1, 1, 1, 3, 2, 2, 2])->skipWhile(Lambda::isLessThan(3))->toArray());

        $this->assertEquals(
            [],
            sequence([2, 3, 4])->skipWhile(Lambda::isLessThan(5))->toArray());

        $this->assertEquals(
            [2, 3, 4],
            sequence([2, 3, 4])->skipWhile(Lambda::isLessThan(2))->toArray());
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
            sequence([1, 1, 1, 2, 1, 1, 1])->takeWhile(Lambda::isLessThan(2))->toArray());

        $this->assertEquals(
            [],
            sequence([2, 3, 4])->takeWhile(Lambda::isLessThan(2))->toArray());

        $this->assertEquals(
            [2, 3, 4],
            sequence([2, 3, 4])->takeWhile(Lambda::isLessThan(5))->toArray());
    }

    public function testSlice()
    {
        $this->assertEquals([7, 8, 9], sequence(self::SOURCE)->slice(6, 3)->toArray());
        $this->assertEquals(self::SOURCE, sequence(self::SOURCE)->slice(0, 10)->toArray());
    }

    public function testFold()
    {
        $this->assertEquals(0, sequence([])->fold(0, Lambda::multiply()));
        $this->assertEquals(1, sequence([])->fold(1, Lambda::multiply()));
        $this->assertEquals(24, sequence([2, 3, 4])->fold(1, Lambda::multiply()));

        $this->assertEquals(10, sequence([1, 2, 3, 4])->fold(0, Lambda::add()));
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

        $this->assertTrue($odds->any(Lambda::isOdd()));
        $this->assertFalse($odds->any(Lambda::isEven()));

        $this->assertFalse($evens->any(Lambda::isOdd()));
        $this->assertTrue($evens->any(Lambda::isEven()));

        $this->assertTrue($mixed->any(Lambda::isOdd()));
        $this->assertTrue($mixed->any(Lambda::isEven()));
    }

    public function testFirst()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(1, $source->first());
        $this->assertEquals(11, $source->first(function ($x) {return $x > 10;}));

        $this->expectException(\LogicException::class);
        $source->first(Lambda::isGreaterThan(12));
    }

    public function testFirstOrNull()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(1, $source->firstOrNull());
        $this->assertEquals(11, $source->firstOrNull(Lambda::isGreaterThan(10)));
        $this->assertEquals(null, $source->firstOrNull(Lambda::isGreaterThan(12)));
    }

    public function testLast()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(12, $source->last());
        $this->assertEquals(10, $source->last(Lambda::isLessThan(11)));

        $this->expectException(\LogicException::class);
        $source->last(Lambda::isGreaterThan(12));
    }

    public function testLastOrNull()
    {
        $source = sequence([1, 10, 11, 12]);

        $this->assertEquals(12, $source->last());
        $this->assertEquals(10, $source->last(Lambda::isLessThan(11)));

        $this->expectException(\LogicException::class);
        $this->assertEquals(null, $source->last(Lambda::isGreaterThan(12)));
    }

    public function testSingle()
    {
        $this->assertEquals(1, sequence([1])->single());
        $this->assertEquals(1, sequence([1])->singleOrNull());

        $this->assertEquals(2, sequence([1, 2, 3])->single(Lambda::isEven()));
        $this->assertEquals(2, sequence([1, 2, 3])->singleOrNull(Lambda::isEven()));

        $this->assertEquals(null, sequence([1, 2, 3])->singleOrNull(Lambda::isGreaterThan(3)));

        $this->expectException(\LogicException::class);
        sequence([1, 2, 3])->single(Lambda::isGreaterThan(3));
    }

    public function testExcept()
    {
        $this->assertEquals(
            [1, 2, 2, 3],
            sequence([0, 1, 2, 2, 3, 5])->except([5, 0])->toArray());
    }

    public function testIntersect()
    {
        $this->assertEquals(
            [1, 2, 2, 3],
            sequence([0, 1, 2, 2, 3, 5])->intersect([3, 2, 1])->toArray());
    }

    public function testZip()
    {
        $generatorSequence = sequence((function () {yield 1;yield 2;yield 3;yield 4;})());
        $arraySequence = sequence([10, 11, 12]);

        $strJoin = function ($x, $y) {
            return $x . '-' . $y;
        };

        $this->assertEquals(
            ['1-10', '2-11', '3-12'],
            $generatorSequence->zip($arraySequence, $strJoin)->toArray());
        $this->assertEquals(
            ['10-1', '11-2', '12-3'],
            $arraySequence->zip($generatorSequence, $strJoin)->toArray());
    }

    public function testChunk()
    {
        $s = xrange(1, 6);

        $this->assertEquals(
            [[1], [2], [3], [4], [5], [6]],
            $s->chunk(1)->map(Lambda::toArray())->toArray());

        $this->assertEquals(
            [[1, 2], [3, 4], [5, 6]],
            $s->chunk(2)->map(Lambda::toArray())->toArray());

        $this->assertEquals(
            [[1, 2, 3, 4, 5], [6]],
            $s->chunk(5)->map(Lambda::toArray())->toArray());

        $this->assertEquals(
            [],
            LazySequence::empty()->chunk(5)->map(Lambda::toArray())->toArray());
    }

    public function testJoin()
    {
        $people = sequence([
            ['id' => 1, 'name' => 'Bob'],
            ['id' => 2, 'name' => 'Jim'],
            ['id' => 3, 'name' => 'Joe'],
        ]);

        $possessions = sequence([
            ['personId' => 1, 'name' => 'pants'],
            ['personId' => 3, 'name' => 'car'],
            ['personId' => 3, 'name' => 'pillow'],
        ]);

        $this->assertEquals(
            [],
            $people
                ->join(
                    $possessions,
                    Lambda::selectKey('name'),
                    Lambda::selectKey('name'),
                    Lambda::constant('This should never get called'))
                ->toArray());

        $expected = [
            ['name' => 'Bob', 'possession' => 'pants'],
            ['name' => 'Joe', 'possession' => 'car'],
            ['name' => 'Joe', 'possession' => 'pillow'],
        ];

        $this->assertEquals(
            $expected,
            $people
                ->join(
                    $possessions,
                    Lambda::selectKey('id'),
                    Lambda::selectKey('personId'),
                    function ($person, $possession) {
                        return ['name' => $person['name'], 'possession' => $possession['name']];
                    })
                ->toArray());

        $this->assertEquals(
            $expected,
            $possessions
                ->join(
                    $people,
                    Lambda::selectKey('personId'),
                    Lambda::selectKey('id'),
                    function ($possession, $person) {
                        return ['name' => $person['name'], 'possession' => $possession['name']];
                    })
                ->toArray());
    }

    public function testImplode()
    {
        $this->assertEquals(
            '1, 2, 3, 4, 5',
            sequence([1, 2, 3, 4, 5])->implode(', '));
    }

    public function testEmpty()
    {
        $this->assertEquals([], sequence([])->toArray());
        $this->assertEquals([], LazySequence::empty()->toArray());
    }
}
