<?php
namespace Jcstrandburg\Demeter;

function sequence(iterable $seq): Sequence
{
    return $seq instanceof Sequence ? $seq : new LazySequence($seq);
}

function collect(iterable $seq): Collection
{
    return $seq instanceof Collection ? $seq : new ArrayCollection($seq);
}

function set(iterable $seq): Set
{
    return $seq instanceof Set ? $seq : new HashSet($seq);
}

function dictionary(iterable $dict): Dictionary
{
    return $dict instanceof Dictionary ? $dict : new ArrayDictionary($dict);
}

function xrange(int $start, int $end, int $step = 1): Sequence
{
    return sequence(range($start, $end, $step));
}

function repeat(iterable $seq, int $count): Sequence
{
    if ($count == 0) {
        return LazySequence::empty();
    } else if ($count < 0) {
        throw new \InvalildArgumentException("\$count must be non-negative");
    }

    return xrange(1, $count)->flatMap(Lambda::constant($seq));
}

function infinite(iterable $seq): Sequence
{
    return sequence(new \InfiniteIterator(as_traversable($seq)));
}

function as_traversable(iterable $iterable): \Traversable
{
    if ($iterable instanceof \Traversable) {
        return $iterable;
    } else if (is_array($iterable)) {
        return new \ArrayIterator($iterable);
    }

    throw new \InvalidArgumentException("Expected \$iterable to of type 'Traversable' or 'array', got type: " . gettype($iterable));
}

function pick_array(iterable $iterable, int $count): array
{
    if ($count > 0) {
        return iterator_to_array(new \LimitIterator(as_traversable($iterable), 0, $count), false);
    } else if ($count == 0) {
        return [];
    } else {
        throw new \InvalidArgumentException("\$count must be non-negative");
    }
}

function ezhash($value): string
{
    if (is_array($value)) {
        return md5(serialize($value));
    } else if (is_object($value)) {
        return md5(spl_object_hash($value));
    } else {
        return md5($value);
    }
}
