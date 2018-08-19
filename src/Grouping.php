<?php
namespace Jcstrandburg\Demeter;

class Grouping extends Collection
{
    public function __construct(iterable $seq, $groupKey)
    {
        parent::__construct($seq);
        $this->groupKey = $groupKey;
    }

    public function getGroupKey()
    {
        return $this->groupKey;
    }
}
