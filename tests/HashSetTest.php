<?php
namespace Tests;

use function \Jcstrandburg\Demeter\sequence;
use Jcstrandburg\Demeter\HashSet;
use PHPUnit\Framework\TestCase;

class HashSetTest extends TestCase
{
    public function testAddAndRemove()
    {
        $empty = new HashSet();
        $a = $empty->add(1);
        $b = $a->add(2);
        $c = $b->remove(1);
        $d = $c->add(2);
        $e = $d->addMany([2, 3, 4]);
        $f = $e->removeMany([4, 5, 6]);

        $this->assertEquals([], $empty->toArray());
        $this->assertEquals([1], $a->toArray());
        $this->assertEquals([1, 2], $b->toArray());
        $this->assertEquals([2], $c->toArray());
        $this->assertEquals([2], $d->toArray());
        $this->assertEquals([2, 3, 4], $e->toArray());
        $this->assertEquals([2, 3], $f->toArray());
    }

    public function testWithHashFunction()
    {
        $source = [
            new TestObject('james', 'Jimbo'),
            new TestObject('robert', 'Bob'),
            new TestObject('james', 'Jimmy'),
            new TestObject('james', 'Jim'),
            new TestObject('robert', 'Robert'),
            new TestObject('james', 'James'),
            new TestObject('robert', 'Robby'),
        ];
        $expectedOutput = [$source[0], $source[1]];

        $set = sequence($source)->asSet(function ($x) {return $x->name;});
        $this->assertEquals($expectedOutput, $set->toArray());
        $this->assertEquals($expectedOutput, $set->add($source[0])->toArray());
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
