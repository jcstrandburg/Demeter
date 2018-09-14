<?php
namespace Jcstrandburg\Demeter;

class ArrayDictionary extends ArrayCollection implements Dictionary
{
    public function __construct(array $data)
    {
        $this->dict = $data;
    }

    public function getKeys(): Collection
    {
        return new ArrayCollection(array_keys($this->dict));
    }

    public function getValues(): Collection
    {
        return new ArrayCollection(array_values($this->dict));
    }

    public function add($key, $value): Dictionary
    {
        return $this->addCore([$key => $value]);
    }

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

    public function remove($key): Dictionary
    {
        return $this->removeCore([$key]);
    }

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

    public function setItem($key, $value): Dictionary
    {
        return $this->setCore([$key => $value]);
    }

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

    public function containsKey($key): bool
    {
        return array_key_exists($key, $this->dict);
    }

    public function toArray(): array
    {
        return $this->dict;
    }

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

    /**
     * @property    array
     */
    private $dict;
}
