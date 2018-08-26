<?php
namespace Jcstrandburg\Demeter;

class ArrayGrouping extends ArrayCollection implements Grouping
{
    private $groupKey;

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
