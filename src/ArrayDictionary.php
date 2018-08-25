<?php
namespace Jcstrandburg\Demeter;

class ArrayDictionary extends ArrayCollection implements Dictionary
{
    private $dict;

    public function __construct(array $data)
    {
        $this->dict = $data;
    }

    /**
     * @return  Collection  All keys
     */
    public function getKeys(): Collection
    {
        return new ArrayCollection(array_keys($this->dict));
    }

    /**
     * @return  Collection  All values
     */
    public function getValues(): Collection
    {
        return new ArrayCollection(array_values($this->dict));
    }

    /**
     * Adds a the given key/value pair to the dictionary
     * @param   mixed   $key
     * @param   mixed   $value
     * @throws  \LogicException if the given key is already present.
     * @return  Dictionary
     */
    public function add($key, $value): Dictionary
    {
        return $this->addCore([$key => $value]);
    }

    /**
     * Adds the key/value pairs from the given dictionary to the dictionary
     * @param   iterable   $dict
     * @throws  \LogicException if any of the given keys are already present.
     * @return  Dictionary
     */
    public function addMany(iterable $dict): Dictionary
    {
        return $this->addCore($dict);
    }

    private function addCore(iterable $dict): Dictionary
    {
        if (count($dict) == 0) {
            return $this;
        }

        $data = $this->dict;
        foreach ($dict as $key => $value) {
            if (array_key_exists($key, $data)) {
                throw new \LogicException("Duplicate key");
            }
            $data[$key] = $value;
        }

        return new ArrayDictionary($data);
    }

    /**
     * Removes the given key from the dictionary.
     * @param   mixed   $key
     * @return  Dictionary
     */
    public function remove($key): Dictionary
    {
        return $this->removeCore([$key]);
    }

    /**
     * Removes the given keys from the dictionary.
     * @param   mixed   $keys
     * @return  Dictionary
     */
    public function removeMany(iterable $keys): Dictionary
    {
        return $this->removeCore($keys);
    }

    private function removeCore(iterable $keys): Dictionary
    {
        $data = $this->dict;
        foreach ($keys as $key) {
            unset($data[$key]);
        }

        if (count($data) == count($this->dict)) {
            return $this;
        } else {
            return new ArrayDictionary($data);
        }
    }

    /**
     * Adds or replaces a the given key/value pair to the dictionary
     * @param   mixed   $key
     * @param   mixed   $value
     * @return  Dictionary
     */
    public function setItem($key, $value): Dictionary
    {
        return $this->setCore([$key => $value]);
    }

    /**
     * Adds or replaces the key/value pairs from the given dictionary to the dictionary
     * @param   iterable   $dict
     * @return  Dictionary
     */
    public function setMany(iterable $dict): Dictionary
    {
        return $this->setCore($dict);
    }

    private function setCore(iterable $dict): Dictionary
    {
        $data = $this->dict;
        foreach ($dict as $key => $value) {
            $data[$key] = $value;
        }

        return new ArrayDictionary($data);
    }

    /**
     * Checks whether the dictionary contains the given key
     * @return  bool
     */
    public function containsKey($key): bool
    {
        return array_key_exists($key, $this->dict);
    }

    /**
     * Converts the dicitionary to a native PHP array, preserving keys
     * @return  array
     */
    public function toArray(): array
    {
        return $this->dict;
    }

    /**
     * Maps values into a new Dictionary, perserving the keys
     * @param   callable    $mapper
     * @return  Dictionary
     */
    public function dictionaryMap(callable $mapper): Dictionary
    {
        return new ArrayDictionary(array_map($mapper, $this->dict));
    }

    public function offsetExists($key)
    {
        return $this->containsKey($key);
    }

    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->dict)) {
            return $this->dict[$key];
        } else {
            throw new \OutOfBoundsException("Key does not exist");
        }
    }

    public function offsetSet($key, $value)
    {
        throw new \BadMethodCallException("ArrayDictionary does not support offsetSet");
    }

    public function offsetUnset($key)
    {
        throw new \BadMethodCallException("ArrayDictionary does not support offsetUnset");
    }

    /**
     * @param   iterable    $keyValuePairs  [[key => value], [key => value]]
     * @return  Dictionary
     */
    public static function fromPairs(iterable $keyValuePairs): Dictionary
    {
        $dict = [];
        foreach ($keyValuePairs as list($key, $value)) {
            if (array_key_exists($key, $dict)) {
                throw new \LogicException("Duplicate key");
            }

            $dict[$key] = $value;
        }
        return new ArrayDictionary($dict);
    }
}
