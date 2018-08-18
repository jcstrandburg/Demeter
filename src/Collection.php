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
}
