<?php
namespace Jcstrandburg\Demeter;

interface Dictionary extends Collection, \ArrayAccess
{
    /**
     * @return  Collection  All keys
     */
    public function getKeys(): Collection;

    /**
     * @return  Collection  All values
     */
    public function getValues(): Collection;

    /**
     * Adds a the given key/value pair to the dictionary
     * @param   mixed   $key
     * @param   mixed   $value
     * @throws  \LogicException if the given key is already present.
     * @return  Dictionary
     */
    public function add($key, $value): Dictionary;

    /**
     * Adds the key/value pairs from the given dictionary to the dictionary
     * @param   iterable   $dict
     * @throws  \LogicException if any of the given keys are already present.
     * @return  Dictionary
     */
    public function addMany(iterable $dict): Dictionary;

    /**
     * Removes the given key from the dictionary.
     * @param   mixed   $key
     * @return  Dictionary
     */
    public function remove($key): Dictionary;

    /**
     * Removes the given keys from the dictionary.
     * @param   mixed   $keys
     * @return  Dictionary
     */
    public function removeMany(iterable $keys): Dictionary;

    /**
     * Adds or replaces a the given key/value pair to the dictionary
     * @param   mixed   $key
     * @param   mixed   $value
     * @return  Dictionary
     */
    public function setItem($key, $value): Dictionary;

    /**
     * Adds or replaces the key/value pairs from the given dictionary to the dictionary
     * @param   iterable   $dict
     * @return  Dictionary
     */
    public function setMany(iterable $dict): Dictionary;

    /**
     * Checks whether the dictionary contains the given key
     * @return  bool
     */
    public function containsKey($key): bool;

    /**
     * Converts the dicitionary to a native PHP array, preserving keys
     * @return  array
     */
    public function toArray(): array;

    /**
     * Maps values into a new Dictionary, perserving the keys
     * @param   callable    $mapper
     * @return  Dictionary
     */
    public function dictionaryMap(callable $mapper): Dictionary;
}
