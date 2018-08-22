<?php
namespace Tests;

use function Jcstrandburg\Demeter\sequence;
use function Jcstrandburg\Demeter\set;
use Jcstrandburg\Demeter\HashSet;
use Jcstrandburg\Demeter\Lambda;
use PHPUnit\Framework\TestCase;

class HashSetTest extends TestCase
{
    public function testFactoryFunction()
    {
        $s = set([1, 2, 3]);
        $this->assertInstanceOf(HashSet::class, $s);

        $t = set($s);
        $this->assertEquals($s, $t);
    }

    public function testAddAndRemove()
    {
        $empty = new HashSet();
        $this->assertEquals([], $empty->toArray());

        $a = $empty->add(1);
        $this->assertEquals([1], $a->toArray());

        $b = $a->add(2);
        $this->assertEquals([1, 2], $b->toArray());

        $c = $b->remove(1);
        $this->assertEquals([2], $c->toArray());

        $d = $c->add(2);
        $this->assertEquals([2], $d->toArray());

        $e = $d->addMany([2, 3, 4]);
        $this->assertEquals([2, 3, 4], $e->toArray());

        $f = $e->removeMany([4, 5, 6]);
        $this->assertEquals([2, 3], $f->toArray());
    }

    public function testWithComparerAndHashFunction()
    {
        $source = [
            new TestObject('james', 'Jimbo'),
            new TestObject('robert', 'Bob'),
            new TestObject('james', 'James'),
            new TestObject('robert', 'Robby'),
        ];

        $expected = [$source[0], $source[1]];

        $set = sequence($source)->asSet(function ($x, $y) {return $x->name === $y->name;}, function ($x) {return $x->name;});

        foreach ($source as $i => $x) {
            $this->assertTrue($set->contains($x), "Set must contain element $i");
        }

        $this->assertEquals($expected, $set->toArray());
        $this->assertEquals($expected, $set->add($source[2])->toArray());
        $this->assertEquals([$source[1]], $set->remove($source[2])->toArray());
    }

    public function testWithHashFunction()
    {
        $source = [
            new TestObject('james', 'Jimbo'),
            new TestObject('robert', 'Bob'),
            new TestObject('james', 'James'),
            new TestObject('robert', 'Robby'),
        ];

        $expected = sequence([0, 2, 1, 3])->map(Lambda::getOffset($source))->toArray();
        $expected2 = sequence([0, 2, 3])->map(Lambda::getOffset($source))->toArray();

        $set = sequence($source)->asSet(null, function ($x) {return $x->name;});

        foreach ($source as $i => $x) {
            $this->assertTrue($set->contains($x), "Set must contain element $i");
        }

        $this->assertEquals($expected, $set->toArray());
        $this->assertEquals($expected, $set->add($source[0])->toArray());

        $this->assertEquals(count($source) - 1, $set->remove($source[1])->count());
        $this->assertEquals($expected2, $set->remove($source[1])->toArray());
    }
}

class TestObject
{
    public function __construct($name, $alias)
    {
        $this->name = $name;
        $this->alias = $alias;
    }

}
