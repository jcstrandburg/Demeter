<?php
namespace Jcstrandburg\Demeter;

class SkipWhileIterator extends \IteratorIterator
{
    private $reject;

    public function __construct(iterable $seq, callable $reject)
    {
        parent::__construct(as_traversable($seq));
        $this->reject = $reject;
    }

    public function rewind()
    {
        parent::rewind();

        while ($this->valid() && ($this->reject)($this->current())) {
            $this->next();
        }
    }
}
