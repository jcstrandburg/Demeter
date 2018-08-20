<?php
namespace Tests;

use function Jcstrandburg\Demeter\dictionary;
use function Jcstrandburg\Demeter\sequence;
use Jcstrandburg\Demeter\Dictionary;
use PHPUnit\Framework\TestCase;

class DictionaryTest extends TestCase
{
    public function testFactoryFunction()
    {
        $x = dictionary(['a' => 'abc', 'b' => 'def']);
        $this->assertInstanceOf(Dictionary::class, $x);
        $this->assertEquals('abc', $x['a']);

        $y = dictionary($x);
        $this->assertEquals($x, $y);
    }

    public function testToDictionaryWithDuplicateKeys()
    {
        $this->expectException(\LogicException::class);
        $dict = sequence([
            ['name' => 'jimmy'],
            ['name' => 'james'],
            ['name' => 'jimmy'],
        ])->toDictionary(function ($x) {
            return $x['name'];
        });
    }

    public function testDictionaryGetNonExistentKey()
    {
        $dict = new Dictionary(['key1' => 0]);
        $this->assertEquals(0, $dict['key1']);

        $this->expectException(\OutOfBoundsException::class);
        $_ = $dict['dummy'];
    }

    public function testAdd()
    {
        $this->assertEquals(
            [
                'key1' => 100,
                'key2' => 200,
                'key3' => 400,
                'key4' => 500,
            ],
            $this->getTestDictionary()->add('key4', 500)->toArray());

        $this->assertEquals(
            [
                'key1' => 100,
                'key2' => 200,
                'key3' => 400,
                'key4' => 500,
                'key5' => 600,
            ],
            $this->getTestDictionary()->addMany(['key4' => 500, 'key5' => 600])->toArray());

        $this->expectException(\LogicException::class);
        $this->getTestDictionary()->add('key1', 101);
    }

    public function testRemove()
    {
        $this->assertEquals(
            [
                'key1' => 100,
                'key3' => 400,
            ],
            $this->getTestDictionary()->remove('key2')->toArray());

        $this->assertEquals(
            [
                'key1' => 100,
                'key2' => 200,
                'key3' => 400,
            ],
            $this->getTestDictionary()->remove('key4')->toArray());

        $this->assertEquals(
            [
                'key2' => 200,
            ],
            $this->getTestDictionary()->removeMany(['key1', 'key3'])->toArray());
    }

    public function testSetItem()
    {
        $this->assertEquals(
            [
                'key1' => 100,
                'key2' => -1,
                'key3' => 400,
            ],
            $this->getTestDictionary()->setItem('key2', -1)->toArray());

        $this->assertEquals(
            [
                'key1' => 101,
                'key2' => 200,
                'key3' => 400,
                'key4' => 500,
            ],
            $this->getTestDictionary()->setMany(['key1' => 101, 'key4' => 500])->toArray());
    }

    public function testDictionaryMap()
    {
        $this->assertEquals(
            [
                'key1' => 50,
                'key2' => 100,
                'key3' => 200,
            ],
            $this->getTestDictionary()->dictionaryMap(function ($x) {return $x / 2;})->toArray());
    }

    public function testGetKeys()
    {
        $this->assertEquals(['key1', 'key2', 'key3'], $this->getTestDictionary()->getKeys()->toArray());
    }

    public function testGetValues()
    {
        $this->assertEquals([100, 200, 400], $this->getTestDictionary()->getValues()->toArray());
    }

    private function getTestDictionary()
    {
        return new Dictionary([
            'key1' => 100,
            'key2' => 200,
            'key3' => 400,
        ]);
    }
}
