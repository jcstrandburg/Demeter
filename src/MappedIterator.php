<?php
namespace Jcstrandburg\Demeter;

class MappedIterator extends \IteratorIterator
{
    private $mapper;

    public function __construct(iterable $seq, callable $mapper)
    {
        if ($mapper == null) {
            throw new \ArgumentException("Cannot be null");
        }

        parent::__construct(as_iterator($seq));
        $this->mapper = $mapper;
    }

    public function current()
    {
        return ($this->mapper)(parent::current());
    }
}
