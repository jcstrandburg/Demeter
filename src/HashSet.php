<?php
namespace Jcstrandburg\Demeter;

/**
 * A collection in which each item is guaranteed to be unique by hash
 */
class HashSet extends Collection
{
    private $hashMap;
    private $hashFunction;

    /**
     * @param   iterable    $seq    The source data
     * @param   callable    $hashFunction   The hash function to use for item comparisons. If none is provided then the default algorithm will be used.
     * @param   bool    $isClone    Used internally to avoid rehashing the entire set when cloning.
     */
    public function __construct(iterable $seq = null, callable $hashFunction = null, bool $isClone = false)
    {
        $this->hashFunction = $hashFunction ?? '\Jcstrandburg\Demeter\ezhash';

        if ($isClone) {
            $this->hashMap = $seq;
        } else {
            $this->hashMap = [];
            foreach (($seq ?? []) as $ele) {
                $key = ($this->hashFunction)($ele);
                if (!array_key_exists($key, $this->hashMap)) {
                    $this->hashMap[$key] = $ele;
                }
            }
        }

        parent::__construct($this->hashMap);
    }

    /**
     * Returns a copy of this set guaranteed to contain the given item (or one with an identical hash)
     * @param   mixed   $item
     */
    public function add($item): HashSet
    {
        return $this->addCore([$item]);
    }

    /**
     * Returns a copy of this set guaranteed to contain the given items (or items with a identical hashes)
     * @param   mixed   $item
     */
    public function addMany(iterable $items): HashSet
    {
        return $this->addCore($items);
    }

    private function addCore(iterable $items): HashSet
    {
        $hashMap = $this->hashMap;
        foreach ($items as $item) {
            $key = ($this->hashFunction)($item);
            if (!array_key_exists($key, $hashMap)) {
                $hashMap[$key] = $item;
            }
        }

        return (count($hashMap) == count($this->hashMap)) ? $this : new HashSet($hashMap, $this->hashFunction, true);
    }

    /**
     * Returns a copy of this set guaranteed not to contain the given item (or any with an identical hash)
     * @param   mixed   $item
     */
    public function remove($item): HashSet
    {
        return $this->removeCore([$item]);

        $key = ($this->hashFunction)($item);
        if (!array_key_exists($key, $this->hashMap)) {
            return this;
        }

        $clonedHashMap = $this->hashMap;
        $clonedHashMap[$key] = $item;
        unset($clonedHashMap[$key]);
        return new HashSet($clonedHashMap, $this->hashFunction, true);
    }

    /**
     * Returns a copy of this set guaranteed not to contain the given items (or any with identical hashes)
     * @param   mixed   $item
     */
    public function removeMany(iterable $items): HashSet
    {
        return $this->removeCore($items);
    }

    private function removeCore(iterable $items): HashSet
    {
        $hashMap = $this->hashMap;
        foreach ($items as $item) {
            $key = ($this->hashFunction)($item);
            if (array_key_exists($key, $this->hashMap)) {
                unset($hashMap[$key]);
            }
        }

        return (count($hashMap) == count($this->hashMap)) ? $this : new HashSet($hashMap, $this->hashFunction, true);
    }

    /**
     * Returns true if this set contains the given item (or one with an identical hash)
     * @param   mixed   $item
     */
    public function contains($item)
    {
        return array_key_exists($this->getHash($ele), $this->hashMap);
    }

    /**
     * Converts the given sequence and hash function to a HashSet, intelligently detecting situations where the sequence already is a HashSet.
     * @param   iterable    $seq
     * @param   callable|null   $hashFunction
     */
    public static function from(iterable $seq, ?callable $hashFunction): HashSet
    {
        $f = $hashFunction ?? ezhash();

        if ($seq instanceof HashSet && $seq->hashFunction === $f) {
            return $seq;
        } else {
            return new HashSet(is_array($seq) ? $seq : iterator_to_array($seq), $hashFunction);
        }
    }
}
