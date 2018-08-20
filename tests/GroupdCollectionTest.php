<?php
namespace Tests;

use function Jcstrandburg\Demeter\sequence;
use Jcstrandburg\Demeter\GroupedCollection;
use Jcstrandburg\Demeter\Lambda;
use PHPUnit\Framework\TestCase;

class GroupedCollectionTest extends TestCase
{
    public function testGroupedCollection()
    {
        $g = new GroupedCollection([
            'a' => [],
            'b' => [1, 2, 3],
            'c' => [[4], [5], [6]],
        ]);

        $this->assertEquals([], $g['a']->toArray());
        $this->assertEquals([1, 2, 3], $g['b']->toArray());
        $this->assertEquals([[4], [5], [6]], $g['c']->toArray());
        $this->assertEquals([], $g['d']->toArray());
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

        $this->assertEquals(['cat', 'dog', 'bat'], $groups->getGroupKeys());
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
