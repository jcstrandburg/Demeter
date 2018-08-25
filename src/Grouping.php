<?php
namespace Jcstrandburg\Demeter;

class Grouping extends ArrayCollection
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
