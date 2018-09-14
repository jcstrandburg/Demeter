<?php
namespace Jcstrandburg\Demeter;

class SkipWhileIterator extends \IteratorIterator
{
    public function __construct(iterable $seq, callable $reject)
    {
        parent::__construct(as_iterator($seq));
        $this->reject = $reject;
    }

    public function rewind()
    {
        parent::rewind();

        while ($this->valid() && ($this->reject)($this->current())) {
            $this->next();
        }
    }

    /**
     * @property    callable
     */
    private $reject;
}
