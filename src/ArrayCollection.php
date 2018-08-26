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

    public function count()
    {
        return $this->count;
    }

    private static $_empty;

    function empty() {
        return self::$_empty ?? self::$_empty = new ArrayCollection([]);
    }
}
