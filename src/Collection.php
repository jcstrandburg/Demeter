<?php
namespace Jcstrandburg\Demeter;

/**
 * A materialized, Countable implementation of Sequence
 */
class Collection extends Sequence implements \Countable
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
        if (self::$_empty == null) {
            self::$_empty = new Collection([]);
        }

        return self::$_empty;
    }
}
