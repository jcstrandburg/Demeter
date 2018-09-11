<?php
namespace Jcstrandburg\Demeter;

class TakeWhileIterator extends \IteratorIterator
{
    private $accept;

    public function __construct(iterable $seq, callable $accept)
    {
        parent::__construct(as_iterator($seq));
        $this->accept = $accept;
    }

    public function valid()
    {
        $innerIterator = $this->getInnerIterator();
        return $innerIterator->valid() && ($this->accept)($innerIterator->current());
    }
}
