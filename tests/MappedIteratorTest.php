<?php
namespace Tests;

use Jcstrandburg\Demeter\MappedIterator;
use PHPUnit\Framework\TestCase;

class MappedIteratorTest extends TestCase
{
    private const SOURCE = [1, 2, 3, 4, 5];

    public function testIdentityMap(): void
    {
        $this->doTest(self::SOURCE, function ($x) {return $x;});
    }

    public function testMapWithPlusOne(): void
    {
        $plusOne = function ($x) {return $x + 1;};
        $this->doTest(array_map($plusOne, self::SOURCE), $plusOne);
    }

    private function doTest(array $expected, callable $mapper): void
    {
        $this->assertEquals(
            $expected,
            iterator_to_array(new MappedIterator(self::SOURCE, $mapper))
        );
    }
}
