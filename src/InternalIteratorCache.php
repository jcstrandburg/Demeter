<?php
namespace Jcstrandburg\Demeter;

/**
 * @internal    Use this at your own peril
 */
final class InternalIteratorCache
{
    public function __construct(iterable $seq)
    {
        $this->internalArray = [];
        $this->sourceIterator = as_iterator($seq);
        $this->sourceIterator->rewind();
    }

    public function hasIndex(int $index)
    {
        if ($index < 0) {
            return false;
        }

        $this->fillInternalArrayTo($index);

        if (count($this->internalArray) > $index) {
            return true;
        }

        return false;
    }

    public function getIndex(int $index)
    {
        if ($this->hasIndex($index)) {
            return $this->internalArray[$index];
        }

        throw new \OutOfBoundsException();
    }

    private function fillInternalArrayTo(int $index)
    {
        if ($this->isSourceIteratorExhausted) {
            return;
        }

        while (count($this->internalArray) <= $index) {
            if ($this->sourceIterator->valid()) {
                $this->internalArray[] = $this->sourceIterator->current();
                $this->sourceIterator->next();
            } else {
                $this->isSourceIteratorExhausted = true;
                return;
            }
        }
    }

    /**
     * @property    array
     */
    private $internalArray;

    /**
     * @property    \Iterator
     */
    private $sourceIterator;

    /**
     * @property    bool
     */
    private $isSourceIteratorExhausted = false;
}
