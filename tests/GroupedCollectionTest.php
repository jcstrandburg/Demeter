<?php
namespace Tests;

use function Jcstrandburg\Demeter\sequence;
use Jcstrandburg\Demeter\ArrayGroupedCollection;
use Jcstrandburg\Demeter\ArrayGrouping;
use Jcstrandburg\Demeter\Lambda;
use PHPUnit\Framework\TestCase;

class GroupedCollectionTest extends TestCase
{
    public function testGroupedCollection()
    {
        $g = new ArrayGroupedCollection([
            'a' => [],
            'b' => [1, 2, 3],
            'c' => [[4], [5], [6]],
        ]);

        $this->assertInstanceOf(ArrayGrouping::class, $g['a']);
        $this->assertEquals([], $g['a']->toArray());

        $this->assertInstanceOf(ArrayGrouping::class, $g['b']);
        $this->assertEquals([1, 2, 3], $g['b']->toArray());

        $this->assertInstanceOf(ArrayGrouping::class, $g['c']);
        $this->assertEquals([[4], [5], [6]], $g['c']->toArray());

        $this->assertInstanceOf(ArrayGrouping::class, $g['d']);
        $this->assertEquals([], $g['d']->toArray());

        foreach ($g as $grouping) {
            $this->assertInstanceOf(ArrayGrouping::class, $grouping);
        }
    }

    public function testGroupBy()
    {
        $groups = sequence([
            ['species' => 'cat', 'name' => 'Whiskers'],
            ['species' => 'dog', 'name' => 'Spot'],
            ['species' => 'cat', 'name' => 'Thelma'],
            ['species' => 'dog', 'name' => 'Diogenes'],
            ['species' => 'bat', 'name' => 'Viktor'],
        ])->groupBy(Lambda::selectKey('species'));

        $this->assertEquals(
            ['cat', 'dog', 'bat'],
            $groups->getGroupKeys()->toArray());

        foreach ($groups->getGroupKeys() as $key) {
            $this->assertEquals($key, $groups[$key]->getGroupKey());
        }

        $this->assertEquals(
            [
                ['species' => 'cat', 'name' => 'Whiskers'],
                ['species' => 'cat', 'name' => 'Thelma'],
            ],
            $groups['cat']->toArray());

        $this->assertEquals(
            [
                ['species' => 'dog', 'name' => 'Spot'],
                ['species' => 'dog', 'name' => 'Diogenes'],
            ],
            $groups['dog']->toArray());

        $this->assertEquals(
            [
                ['species' => 'bat', 'name' => 'Viktor'],
            ],
            $groups['bat']->toArray());

        $this->assertEquals([], $groups['elk']->toArray());
    }
}
