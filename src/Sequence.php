<?php
namespace Jcstrandburg\Demeter;

class Sequence extends \IteratorIterator
{
    /**
     * @var iterable    $seq The source data
     */
    public function __construct(iterable $seq)
    {
        $iterator = $seq instanceof LazyRewindableIterator || $seq instanceof Sequence ? $seq : new LazyRewindableIterator($seq);
        parent::__construct($iterator);
    }

    /**
     * @param   callable    $selector   Mapper function
     * @return  Sequence    A new Sequence in which all elements have been mapped to a new value
     */
    public function map(callable $selector): Sequence
    {
        return new Sequence(new MappedIterator($this, $selector));
    }

    /**
     * @param   callable|null   $selector Mapper (leave null for the identity function). Must return an iterable
     * @return  Sequence    A new Sequence that is a flattening of the iterable mappings of each element in the original sequence.
     */
    public function flatMap(callable $selector = null)
    {
        $selector = $selector ?: function ($x) {return $x;};

        return new Sequence((function () use ($selector) {
            foreach ($this as $ele) {
                $mapped = ($selector)($ele);
                if (!is_iterable($mapped)) {
                    throw new \LogicException("\$selector must return an iterable");
                }

                yield from $mapped;
            }
        })());
    }

    /**
     * @param   callable    $predicate Returns a truthy value if an element should be returned in the new Sequence
     * @return  Sequence    A new sequence where every element passed $predicate
     */
    public function filter(callable $predicate): Sequence
    {
        return new Sequence(new \CallbackFilterIterator($this, $predicate));
    }

    /**
     * Creates a new Sequence with the given item appended to it
     * @param   mixed   $ele
     * @return  Sequence
     */
    public function append($ele): Sequence
    {
        return $this->concat([$ele]);
    }

    /**
     * Creates a new Sequence with the given iterable concatenated to it
     * @param   mixed $ele
     * @return  Sequence
     */
    public function concat(iterable $elements)
    {
        $appendIterator = new \AppendIterator();
        $appendIterator->append($this);
        $appendIterator->append(as_traversable($elements));
        return new Sequence($appendIterator);
    }

    /**
     * Creates a new Sequence without the first $offset elements of this Sequence
     * @param   mixed $ele
     * @return  Sequence
     */
    public function skip(int $offset)
    {
        if ($offset < 0) {
            throw new \IllegalArgumentException("\$count must be non-negative");
        } else if ($offset == 0) {
            return $this;
        } else {
            return $this->limit($offset, -1);
        }
    }

    /**
     * Creates a new Sequence from the first $count elements of this Sequence
     * @param   int $count
     * @return  Sequence
     */
    public function take(int $count)
    {
        if ($count < 0) {
            throw new \IllegalArgumentException("\$count must be non-negative");
        } else if ($count == 0) {
            return self::empty();
        } else {
            return $this->limit(0, $count);
        }
    }

    /**
     * Creates a new Sequence from a limited subsequence of this Sequence
     * @param   int $offset The number of elements to skip in this Sequence
     * @param   int $count  The maximum number of elements in the resulting Sequence
     * @return  Sequence
     */
    public function limit(int $offset, int $count)
    {
        return new Sequence(new \LimitIterator($this, $offset, $count));
    }

    /**
     * Materializes this Sequence to an array with numeric keys.
     * @return  array
     */
    public function toArray()
    {
        return iterator_to_array($this, false);
    }

    public function __toString()
    {
        return "<Sequence>";
    }

    private static $_empty = null;

    /**
     * @return  Sequence An empty Sequence
     */
    function empty() {
        if (self::$_empty == null) {
            self::$_empty = new Sequence([]);
        }
        return self::$_empty;
    }
}
