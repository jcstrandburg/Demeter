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
    public function concat(iterable $elements): Sequence
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
    public function skip(int $offset): Sequence
    {
        if ($offset < 0) {
            throw new \IllegalArgumentException("\$count must be non-negative");
        } else if ($offset == 0) {
            return $this;
        } else {
            return $this->slice($offset, -1);
        }
    }

    /**
     * Creates a new Sequence without all contigous elements at the beginning of the Sequence that don't pass $accept
     * @param   calllable   $accept Callback that returns a truthy value if the current element should be skipped
     */
    public function skipWhile(callable $accept): Sequence
    {
        return new Sequence(new SkipWhileIterator($this, $accept));
    }

    /**
     * Creates a new Sequence from the first $count elements of this Sequence
     * @param   int $count
     * @return  Sequence
     */
    public function take(int $count): Sequence
    {
        if ($count < 0) {
            throw new \IllegalArgumentException("\$count must be non-negative");
        } else if ($count == 0) {
            return self::empty();
        } else {
            return $this->slice(0, $count);
        }
    }

    /**
     * Creates a new Sequence with all elements that pass $accept and are contigous at the beginning of the Sequence
     * @param   calllable   $accept Callback that returns a truthy value if the current element should be taken
     */
    public function takeWhile(callable $accept): Sequence
    {
        return new Sequence(new TakeWhileIterator($this, $accept));
    }

    /**
     * Creates a new Sequence from a limited subsequence of this Sequence
     * @param   int $offset The number of elements to skip in this Sequence
     * @param   int $count  The maximum number of elements in the resulting Sequence
     * @return  Sequence
     */
    public function slice(int $offset, int $count): Sequence
    {
        return new Sequence(new \LimitIterator($this, $offset, $count));
    }

    /**
     * Accumulates and returns a value from each element in the sequence.
     * @param   mixed   $initial    The seed value fed into the accumulator. Must be of the form ($currentValue, $currentElement) -> $nextValue
     * @return  mixed
     */
    public function fold($initial, callable $folder)
    {
        $currentValue = $initial;
        foreach ($this as $ele) {
            $currentValue = ($folder)($currentValue, $ele);
        }
        return $currentValue;
    }

    /**
     * Returns true if the given predicate returns a truthy value for any elements, else false.
     * The sequence will not be evaluated beyond the first elements that passes the predicate.
     * @param   calllable   $predicate
     */
    public function any(callable $predicate): bool
    {
        foreach ($this as $ele) {
            if (($predicate)($ele)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the given predicate returns a truthy value for all elements, else false.
     * The sequence will not be evaluated beyond the first elements that does not pass the predicate.
     * @param   calllable   $predicate
     */
    public function all(callable $predicate): bool
    {
        foreach ($this as $ele) {
            if (!($predicate)($ele)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Groups the elements of the sequence by the value returned by the given key selector.
     * @param   callable    $getGroupKey
     * @return  GroupedCollection
     */
    public function groupBy(callable $getGroupKey): GroupedCollection
    {
        $data = [];

        foreach ($this as $ele) {
            $key = ($getGroupKey)($ele);

            if (!array_key_exists($key, $data)) {
                $data[$key] = [];
            }

            $data[$key][] = $ele;
        }

        return new GroupedCollection($data);
    }

    /**
     * Returns the first element that passes the given predicate, or just the first element if no predicate is provided.
     * Throws an exception if no matching element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function first(callable $predicate = null)
    {
        list($success, $value) = $this->firstCore($predicate);
        if ($success) {
            return $value;
        }

        throw new \LogicException("No matching element found.");
    }

    /**
     * Returns the first element that passes the given predicate, or just the first element if no predicate is provided.
     * Returns null if no element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function firstOrNull(callable $predicate = null)
    {
        list($success, $value) = $this->firstCore($predicate);
        if ($success) {
            return $value;
        }

        return null;
    }

    private function firstCore(?callable $predicate)
    {
        foreach ($this as $ele) {
            if ($predicate === null || ($predicate)($ele)) {
                return [true, $ele];
            }
        }
        return [false, null];
    }

    /**
     * Returns the last element that passes the given predicate, or just the last element if no predicate is provided.
     * Throws an exception if no matching element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function last(callable $predicate = null)
    {
        $values = $this->lastCore($predicate);

        if (count($values) > 0) {
            return end($values);
        }

        throw new \LogicException("No matching element found.");
    }

    /**
     * Returns the last element that passes the given predicate, or just the last element if no predicate is provided.
     * Returns null if no element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function lastOrNull(callable $predicate = null)
    {
        $values = $this->lastCore($predicate);

        if (count($values) > 0) {
            return end($values);
        }

        return null;
    }

    private function lastCore(?callable $predicate)
    {
        return $predicate === null ? $this->toArray() : $this->filter($predicate)->toArray();
    }

    public function single(callable $predicate = null)
    {
        list($success, $value) = $this->firstCore($predicate);
        if ($success) {
            return $value;
        }

        throw new \LogicException("No matching element found");
    }

    public function singleOrNull(callable $predicate = null)
    {
        list($success, $value) = $this->firstCore($predicate);
        if ($success) {
            return $value;
        }

        return null;
    }

    private function singleCore(?callable $predicate)
    {
        $elements = ($predicate === null ? $this->take(2) : $this->filter($predicate)->take(2))->toArray();
        if (count($elements) > 1) {
            throw new \LogicException();
        } else if (count($elements) == 1) {
            return [true, reset($elements)];
        } else {
            return [false, null];
        }
    }

    /**
     * Materializes this Sequence to an array with numeric keys. Keys from the original iterator are not preserved.
     * @return  array
     */
    public function toArray(): array
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
