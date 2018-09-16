<?php
namespace Tests;

use function Jcstrandburg\Demeter\collect;
use function Jcstrandburg\Demeter\dictionary;
use function Jcstrandburg\Demeter\sequence;
use function Jcstrandburg\Demeter\set;
use Jcstrandburg\Demeter\Extensions;
use PHPUnit\Framework\TestCase;

class ExtensionTest extends TestCase
{
    public function setUp()
    {
        $dummy = function () {};

        Extensions::extendSequence('sequenceExt', $dummy);
        Extensions::extendCollection('collectionExt', $dummy);
        Extensions::extendDictionary('dictionaryExt', $dummy);
        Extensions::extendSet('setExt', $dummy);
        Extensions::extendGrouping('groupingExt', $dummy);
        Extensions::extendGroupedCollection('groupedCollectionExt', $dummy);
    }

    public function tearDown()
    {
        Extensions::unextendSequence('sequenceExt');
        Extensions::unextendCollection('collectionExt');
        Extensions::unextendDictionary('dictionaryExt');
        Extensions::unextendSet('setExt');
        Extensions::unextendGrouping('groupingExt');
        Extensions::unextendGroupedCollection('groupedCollectionExt');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExtensions(
        string $methodName,
        bool $shouldSequenceHave,
        bool $shouldCollectionHave,
        bool $shouldSetHave,
        bool $shouldDictHave,
        bool $shouldGroupedCollectionHave,
        bool $shouldGroupingHave) {

        $sequence = sequence([]);
        $collection = collect([]);
        $set = set([]);
        $dict = dictionary([]);
        $groupedCollection = sequence([])->groupBy(function () {return 1;});
        $grouping = sequence([])->groupBy(function () {return 1;})[1];

        $this->callExtension($sequence, $methodName, $shouldSequenceHave);
        $this->callExtension($collection, $methodName, $shouldCollectionHave);
        $this->callExtension($set, $methodName, $shouldSetHave);
        $this->callExtension($dict, $methodName, $shouldDictHave);
        $this->callExtension($groupedCollection, $methodName, $shouldGroupedCollectionHave);
        $this->callExtension($grouping, $methodName, $shouldGroupingHave);
    }

    private function callExtension($instance, $methodName, $shouldHaveExtension)
    {
        try {
            $instance->$methodName();
            $this->assertTrue($shouldHaveExtension, get_class($instance) . " should not have extension method " . $methodName);
        } catch (\BadMethodCallException $e) {
            $this->assertFalse($shouldHaveExtension, get_class($instance) . " should have extension method " . $methodName);
        }
    }

    public function dataProvider()
    {
        return [
            ['sequenceExt', true, true, true, true, true, true],
            ['collectionExt', false, true, true, true, true, true],
            ['setExt', false, false, true, false, false, false],
            ['dictionaryExt', false, false, false, true, false, false],
            ['groupedCollectionExt', false, false, false, false, true, false],
            ['groupingExt', false, false, false, false, false, true],
        ];
    }
}
