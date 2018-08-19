<?php
namespace Jcstrandburg\Demeter;

function sequence(iterable $seq)
{
    return $seq instanceof Sequence ? $seq : new Sequence($seq);
}

function collect(iterable $seq)
{
    return $seq instanceof Collection ? $seq : new Collection($seq);
}

function xrange(int $start, int $end, int $step = 1)
{
    foreach (range($start, $end, $step) as $y) {
        yield $y;
    }
}

function repeat(iterable $seq, int $count)
{
    if ($count == 0) {
        return Sequence::empty();
    } else if ($count < 0) {
        throw new \InvalildArgumentException("\$count must be non-negative");
    }

    return sequence((function () use ($seq, $count) {
        foreach (xrange(1, $count) as $_) {
            yield from $seq;
        }
    })());
}

function infinite(iterable $seq)
{
    return sequence(new \InfiniteIterator(as_traversable($seq)));
}

function as_traversable(iterable $iterable)
{
    if ($iterable instanceof \Traversable) {
        return $iterable;
    } else if (is_array($iterable)) {
        return new \ArrayIterator($iterable);
    }

    throw new \InvalidArgumentException("Expected \$iterable to of type 'Traversable' or 'array', got type: " . gettype($iterable));
}

function pick_array(iterable $iterable, int $count)
{
    if ($count > 0) {
        return iterator_to_array(new \LimitIterator(as_traversable($iterable), 0, $count), false);
    } else if ($count == 0) {
        return [];
    } else {
        throw new \InvalidArgumentException("\$count must be non-negative");
    }
}
