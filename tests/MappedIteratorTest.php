<?php
namespace Tests;

use Jcstrandburg\Demeter\Lambda;
use Jcstrandburg\Demeter\MappedIterator;
use PHPUnit\Framework\TestCase;

class MappedIteratorTest extends TestCase
{
    private const SOURCE = [1, 2, 3, 4, 5];

    public function testIdentityMap(): void
    {
        $this->doTest(self::SOURCE, Lambda::identity());
    }

    public function testMapWithPlusOne(): void
    {
        $this->doTest(array_map(Lambda::plus(1), self::SOURCE), Lambda::plus(1));
    }

    private function doTest(array $expected, callable $mapper): void
    {
        $this->assertEquals(
            $expected,
            iterator_to_array(new MappedIterator(self::SOURCE, $mapper))
        );
    }
}
