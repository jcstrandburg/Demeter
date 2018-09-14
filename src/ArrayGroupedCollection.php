<?php
namespace Jcstrandburg\Demeter;

class ArrayGroupedCollection extends ArrayCollection implements GroupedCollection
{
    private $groupsByKey;

    /**
     * Assumes that callers have already filtered out empty groups.
     * @param   iterable[]  $groupsByKey    An associative array associating group keys to iterables.
     */
    public function __construct(array $groupsByKey)
    {
        foreach ($groupsByKey as $value) {
            if (!is_iterable($value)) {
                throw new \InvalidArgumentException("All values in \$groupsByKey must be iterable");
            }
        }

        $this->groupsByKey = [];
        foreach ($groupsByKey as $key => $group) {
            $this->groupsByKey[$key] = new ArrayGrouping($group, $key);
        }

        parent::__construct($this->groupsByKey);
    }

    public function getGroupKeys(): array
    {
        return array_keys($this->groupsByKey);
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->groupsByKey);
    }

    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->groupsByKey)) {
            return $this->groupsByKey[$key];
        } else {
            return self::getEmptyGroup($key);
        }
    }

    public function offsetSet($key, $value)
    {
        throw new \BadMethodCallException("ArrayGroupedCollection does not support offsetSet");
    }

    public function offsetUnset($key)
    {
        throw new \BadMethodCallException("ArrayGroupedCollection does not support offsetUnset");
    }

    /**
     * @property    array
     */
    private static $emptyGroupsCache = [];

    private static function getEmptyGroup($key)
    {
        if (!array_key_exists($key, self::$emptyGroupsCache)) {
            self::$emptyGroupsCache[$key] = new ArrayGrouping([], $key);
        }

        return self::$emptyGroupsCache[$key];
    }
}
