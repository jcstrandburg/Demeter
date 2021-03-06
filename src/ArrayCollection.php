<?php
namespace Jcstrandburg\Demeter;

/**
 * A materialized, Countable implementation of Sequence
 */
class ArrayCollection extends LazySequence implements Collection
{
    public function __construct(iterable $seq)
    {
        $array = is_array($seq) ? $seq : iterator_to_array($seq);
        $this->count = count($array);
        parent::__construct($array);
    }

    public function count(): int
    {
        return $this->count;
    }

    function empty(): ArrayCollection {
        return self::$_empty ?? self::$_empty = new ArrayCollection([]);
    }

    /**
     * @property    int
     */
    private $count;

    /**
     * @property    ArrayCollection
     */
    private static $_empty;
}
