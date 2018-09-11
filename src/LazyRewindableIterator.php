<?php
namespace Jcstrandburg\Demeter;

/**
 * Caches the elements of it's inner iterable so on rewind it is not re-evaluated
 */
class LazyRewindableIterator implements \Iterator
{
    private $cacheIterator;
    private $sourceIterator;
    private $sourceIteratorIsPrepared = false;

    public function __construct(iterable $seq)
    {
        $this->cacheIterator = new \ArrayIterator([]);
        $this->sourceIterator = as_iterator($seq);
    }

    public function current()
    {
        return $this->cacheIterator->current();
    }

    public function next()
    {
        $this->cacheIterator->next();

        if (!$this->cacheIterator->valid()) {
            $this->sourceIterator->next();
            if ($this->sourceIterator->valid()) {
                $this->cacheIterator->append($this->sourceIterator->current());
            }
        }
    }

    public function valid()
    {
        return $this->cacheIterator->valid() || $this->sourceIterator->valid();
    }

    public function rewind()
    {
        $this->cacheIterator->rewind();

        if (!$this->sourceIteratorIsPrepared) {
            $this->sourceIterator->rewind();
            $this->sourceIteratorIsPrepared = true;

            if ($this->sourceIterator->valid()) {
                $this->cacheIterator->append($this->sourceIterator->current());
            }
        }
    }

    public function key()
    {
        return $this->cacheIterator->key();
    }

    private function selectIterator()
    {
        return $this->cacheIterator->valid() ? $this->cacheIterator : $this->sourceIterator;
    }
}
