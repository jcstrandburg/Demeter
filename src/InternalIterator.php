<?php
namespace Jcstrandburg\Demeter;

/**
 * @internal    Use this at your own peril
 */
final class InternalIterator implements \Iterator
{
    public function __construct(InternalIteratorCache $cache)
    {
        $this->cache = $cache;
    }

    public function current()
    {
        return $this->cache->hasIndex($this->currentIndex)
        ? $this->cache->getIndex($this->currentIndex)
        : null;
    }

    public function next()
    {
        $this->currentIndex++;
    }

    public function valid()
    {
        return $this->cache->hasIndex($this->currentIndex);
    }

    public function rewind()
    {
        $this->currentIndex = 0;
    }

    public function key()
    {
        return $this->currentIndex;
    }

    /**
     * @property    InternalIteratorCache
     */
    private $cache;

    /**
     * @property    int
     */
    private $currentIndex;
}
