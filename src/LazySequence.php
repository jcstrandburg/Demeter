<?php
namespace Jcstrandburg\Demeter;

class LazySequence implements Sequence
{
    use \Jcstrandburg\ExtensionMethods\Extensible;

    /**
     * @var iterable    $seq The source data
     */
    public function __construct(iterable $seq)
    {
        $this->iterator = $seq instanceof InternalIteratorCache ? $seq : new InternalIteratorCache($seq);
    }

    public function map(callable $selector): Sequence
    {
        return new LazySequence(new MappedIterator($this, $selector));
    }

    public function flatMap(callable $selector = null)
    {
        $selector = $selector ?: Lambda::identity();

        return new LazySequence((function () use ($selector) {
            foreach ($this as $ele) {
                $mapped = $selector($ele);
                if (!is_iterable($mapped)) {
                    throw new \LogicException("\$selector must return an iterable");
                }

                yield from $mapped;
            }
        })());
    }

    public function filter(callable $predicate): Sequence
    {
        return new LazySequence(new \CallbackFilterIterator($this->getIterator(), $predicate));
    }

    public function append($ele): Sequence
    {
        return $this->concat([$ele]);
    }

    public function concat(iterable $elements): Sequence
    {
        $appendIterator = new \AppendIterator();
        $appendIterator->append($this->getIterator());
        $appendIterator->append(as_iterator($elements));
        return new LazySequence($appendIterator);
    }

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

    public function skipWhile(callable $accept): Sequence
    {
        return new LazySequence(new SkipWhileIterator($this, $accept));
    }

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

    public function takeWhile(callable $accept): Sequence
    {
        return new LazySequence(new TakeWhileIterator($this, $accept));
    }

    public function slice(int $offset, int $count): Sequence
    {
        return new LazySequence(new \LimitIterator($this->getIterator(), $offset, $count));
    }

    public function fold($initial, callable $folder)
    {
        $currentValue = $initial;
        foreach ($this as $ele) {
            $currentValue = $folder($currentValue, $ele);
        }
        return $currentValue;
    }

    public function any(callable $predicate): bool
    {
        foreach ($this as $ele) {
            if ($predicate($ele)) {
                return true;
            }
        }

        return false;
    }

    public function all(callable $predicate): bool
    {
        foreach ($this as $ele) {
            if (!$predicate($ele)) {
                return false;
            }
        }

        return true;
    }

    public function groupBy(callable $getGroupKey): GroupedCollection
    {
        $data = [];

        foreach ($this as $ele) {
            $key = $getGroupKey($ele);

            if (!array_key_exists($key, $data)) {
                $data[$key] = [];
            }

            $data[$key][] = $ele;
        }

        return new ArrayGroupedCollection($data);
    }

    public function first(callable $predicate = null)
    {
        list($success, $value) = $this->firstCore($predicate);
        if ($success) {
            return $value;
        }

        throw new \LogicException("No matching element found.");
    }

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
            if ($predicate === null || $predicate($ele)) {
                return [true, $ele];
            }
        }
        return [false, null];
    }

    public function last(callable $predicate = null)
    {
        $values = $this->lastCore($predicate);

        if (count($values) > 0) {
            return end($values);
        }

        throw new \LogicException("No matching element found.");
    }

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

    public function toArray(): array
    {
        return iterator_to_array($this, false);
    }

    public function asSet(?callable $compareFunction = null, ?callable $hashFunction = null): HashSet
    {
        return HashSet::from($this, $compareFunction, $hashFunction);
    }

    public function toDictionary(callable $keySelector, ?callable $valueSelector = null): Dictionary
    {
        $getValue = $valueSelector ?? Lambda::identity();

        $keyValuePairs = [];
        foreach ($this as $item) {
            $keyValuePairs[] = [$keySelector($item), $getValue($item)];
        }

        return ArrayDictionary::fromPairs($keyValuePairs);
    }

    public function except(iterable $items, ?callable $equalityFunction = null, ?callable $hashFunction = null): Sequence
    {
        $set = HashSet::from($items, $equalityFunction, $hashFunction);
        return $this->filter(Lambda::setDoesNotContain($set));
    }

    public function intersect(iterable $items, ?callable $equalityFunction = null, ?callable $hashFunction = null): Sequence
    {
        $set = HashSet::from($items, $equalityFunction, $hashFunction);
        return $this->filter(Lambda::setContains($set));
    }

    public function zip(iterable $seq, callable $mapper): Sequence
    {
        $iterator = as_iterator($seq);
        $iterator->rewind();

        return sequence((function () use ($iterator, $mapper) {
            foreach ($this as $x) {
                if (!$iterator->valid()) {
                    return;
                }

                yield $mapper($x, $iterator->current());

                if ($iterator !== $this) {
                    $iterator->next();
                }
            }
        })());
    }

    public function chunk(int $count): Sequence
    {
        return new LazySequence((function () use ($count) {
            $x = $this;

            while ($x->any(Lambda::constant(true))) {
                yield $x->take($count)->collect();
                $x = $x->skip($count);
            }
        })());
    }

    public function join(iterable $rightSequence, callable $leftKeySelector, callable $rightKeySelector, callable $mapResult): Sequence
    {
        $right = sequence($rightSequence);

        $leftDict = $this->groupBy($leftKeySelector)->toDictionary(Lambda::getGroupKey());
        $rightDict = $right->groupBy($rightKeySelector)->toDictionary(Lambda::getGroupKey());

        $keys = $leftDict->getKeys()->intersect($rightDict->getKeys());

        return $keys->flatMap(function ($key) use ($leftDict, $rightDict) {
            return $leftDict[$key]->flatMap(function ($leftElement) use ($key, $rightDict) {
                return $rightDict[$key]->map(function ($rightElement) use ($leftElement) {
                    return [$leftElement, $rightElement];
                });
            });
        })
            ->map(function ($elements) use ($mapResult) {
                return $mapResult($elements[0], $elements[1]);
            });
    }

    public function implode(string $glue): string
    {
        return implode($glue, iterator_to_array($this));
    }

    public function collect(): Collection
    {
        return collect($this);
    }

    public function __toString()
    {
        return "<LazySequence>";
    }

    public function getIterator(): InternalIterator
    {
        return new InternalIterator($this->iterator);
    }

    /**
     * @return  Sequence An empty Sequence
     */
    function empty() {
        return self::$_empty ?? self::$_empty = new LazySequence([]);
    }

    /**
     * @property    Sequence
     */
    private static $_empty = null;

    /**
     * @property    InternalIteratorr
     */
    private $iterator;
}
