<?php
namespace Jcstrandburg\Demeter;

/**
 * A collection in which each item is guaranteed to be unique by hash
 */
class HashSet extends ArrayCollection implements Set
{
    private $hashMap;
    private $equalityFunction;
    private $hashFunction;

    /**
     * @param   iterable|null   $seq    The source data
     * @param   callable|null   $equalityFunction   The function to use for item equality comparison. If none is provided then `===` will be used.
     * @param   callable|null   $hashFunction   The function to use for item hashing. If none is provided then the default algorithm will be used.
     * @param   bool|null   $isClone    Used internally to avoid rehashing the entire set when cloning.
     */
    public function __construct(iterable $seq = null, callable $equalityFunction = null, callable $hashFunction = null, bool $isClone = false)
    {
        $this->hashFunction = $hashFunction ?? '\Jcstrandburg\Demeter\ezhash';
        $this->equalityFunction = $equalityFunction ?? Lambda::areStrictlyEqual();

        if ($isClone) {
            if ($seq === null) {
                throw new ArgumentException("\$seq must not be null if \$isClone is true");
            }

            $this->hashMap = $seq;
        } else {
            $this->hashMap = $this->addItemsToArray([], $seq ?? []);
        }

        $flat = [];
        foreach ($this->hashMap as $bucket) {
            foreach ($bucket as $b) {$flat[] = $b;}
        }
        $this->flatMap = $flat;
        parent::__construct($flat);
    }

    /**
     * Returns a copy of this set guaranteed to contain the given item (or one with an identical hash)
     * @param   mixed   $item
     */
    public function add($item): Set
    {
        return $this->addCore([$item]);
    }

    /**
     * Returns a copy of this set guaranteed to contain the given items (or items with a identical hashes)
     * @param   iterable    $items
     */
    public function addMany(iterable $items): Set
    {
        return $this->addCore($items);
    }

    private function addCore(iterable $items): HashSet
    {
        $hashMap = $this->addItemsToArray($this->hashMap, $items);
        return ($hashMap === $this->hashMap) ? $this : new HashSet($hashMap, $this->equalityFunction, $this->hashFunction, true);
    }

    private function addItemsToArray(array $in, iterable $items): array
    {
        $returnMe = $in;
        foreach ($items as $item) {
            $hash = ($this->hashFunction)($item);

            if (!array_key_exists($hash, $returnMe)) {
                $returnMe[$hash] = [];
            }

            foreach ($returnMe[$hash] as $element) {
                if (($this->equalityFunction)($element, $item) == true) {
                    continue 2;
                }
            }

            $returnMe[$hash][] = $item;
        }

        return $returnMe;
    }

    /**
     * Returns a copy of this set guaranteed not to contain the given item (or any with an identical hash)
     * @param   mixed   $item
     */
    public function remove($item): Set
    {
        return $this->removeCore([$item]);
    }

    /**
     * Returns a copy of this set guaranteed not to contain the given items (or any with identical hashes)
     * @param   iterable    $items
     */
    public function removeMany(iterable $items): Set
    {
        return $this->removeCore($items);
    }

    private function removeCore(iterable $items): HashSet
    {
        $hashMap = $this->removeItemsFromArray($this->hashMap, $items);
        return ($hashMap === $this->hashMap) ? $this : new HashSet($hashMap, $this->equalityFunction, $this->hashFunction, true);
    }

    private function removeItemsFromArray(array $array, iterable $items): array
    {
        $returnMe = $array;

        foreach ($items as $item) {
            $hash = ($this->hashFunction)($item);

            if (!array_key_exists($hash, $returnMe)) {
                continue;
            }

            foreach ($returnMe[$hash] as $key => $element) {
                if (($this->equalityFunction)($element, $item)) {
                    unset($returnMe[$hash][$key]);
                }
            }

            if (count($returnMe[$hash]) == 0) {
                unset($returnMe[$hash]);
            }
        }

        return $returnMe;
    }

    /**
     * Returns true if this set contains the given item (or one with an identical hash)
     * @param   mixed   $item
     */
    public function contains($item): bool
    {
        $hash = ($this->hashFunction)($item);

        if (!array_key_exists($hash, $this->hashMap)) {
            return false;
        }

        foreach ($this->hashMap[$hash] as $element) {
            if (($this->equalityFunction)($element, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Converts the given sequence and hash function to a HashSet, intelligently detecting situations where the sequence already is a HashSet.
     * @param   iterable    $seq
     * @param   callable|null   $compareFunction
     * @param   callable|null   $hashFunction
     */
    public static function from(iterable $seq, ?callable $compareFunction, ?callable $hashFunction): HashSet
    {
        $f = $hashFunction ?? 'Jcstrandburg\Demeter\ezhash';
        $g = $compareFunction ?? \Jcstrandburg\Demeter\Lambda::areStrictlyEqual();

        if ($seq instanceof HashSet && $seq->hashFunction === $f && $seq->compareFunction === $g) {
            return $seq;
        } else {
            return new HashSet(is_array($seq) ? $seq : iterator_to_array($seq), $g, $f);
        }
    }
}
