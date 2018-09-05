<?php
namespace Jcstrandburg\Demeter;

interface Sequence extends \Iterator
{
    /**
     * @param   callable    $selector   Mapper function
     * @return  Sequence    A new Sequence in which all elements have been mapped to a new value
     */
    public function map(callable $selector): Sequence;

    /**
     * @param   callable|null   $selector   Mapper (leave null for the identity function). Must return an iterable
     * @return  Sequence    A new Sequence that is a flattening of the iterable mappings of each element in the original sequence.
     */
    public function flatMap(callable $selector = null);

    /**
     * @param   callable    $predicate  Returns a truthy value if an element should be returned in the new Sequence
     * @return  Sequence    A new sequence where every element passed $predicate
     */
    public function filter(callable $predicate): Sequence;

    /**
     * Creates a new Sequence with the given item appended to it
     * @param   mixed   $ele
     * @return  Sequence
     */
    public function append($ele): Sequence;

    /**
     * Creates a new Sequence with the given iterable concatenated to it
     * @param   iterable    $elements
     * @return  Sequence
     */
    public function concat(iterable $elements): Sequence;

    /**
     * Creates a new Sequence without the first $offset elements of this Sequence
     * @param   int $offset
     * @return  Sequence
     */
    public function skip(int $offset): Sequence;

    /**
     * Creates a new Sequence without all contigous elements at the beginning of the Sequence that don't pass $accept
     * @param   callable   $accept  Callback that returns a truthy value if the current element should be skipped
     */
    public function skipWhile(callable $accept): Sequence;

    /**
     * Creates a new Sequence from the first $count elements of this Sequence
     * @param   int $count
     * @return  Sequence
     */
    public function take(int $count): Sequence;

    /**
     * Creates a new Sequence with all elements that pass $accept and are contigous at the beginning of the Sequence
     * @param   callable   $accept  Callback that returns a truthy value if the current element should be taken
     */
    public function takeWhile(callable $accept): Sequence;

    /**
     * Creates a new Sequence from a limited subsequence of this Sequence
     * @param   int $offset The number of elements to skip in this Sequence
     * @param   int $count  The maximum number of elements in the resulting Sequence
     * @return  Sequence
     */
    public function slice(int $offset, int $count): Sequence;

    /**
     * Accumulates and returns a value from each element in the sequence.
     * @param   mixed   $initial    The seed value fed into the accumulator. Must be of the form ($currentValue, $currentElement) -> $nextValue
     * @param   callable    $folder Accumulator function
     * @return  mixed
     */
    public function fold($initial, callable $folder);

    /**
     * Returns true if the given predicate returns a truthy value for any elements, else false.
     * The sequence will not be evaluated beyond the first elements that passes the predicate.
     * @param   callable   $predicate
     */
    public function any(callable $predicate): bool;

    /**
     * Returns true if the given predicate returns a truthy value for all elements, else false.
     * The sequence will not be evaluated beyond the first elements that does not pass the predicate.
     * @param   callable   $predicate
     */
    public function all(callable $predicate): bool;

    /**
     * Groups the elements of the sequence by the value returned by the given key selector.
     * @param   callable    $getGroupKey
     * @return  GroupedCollection
     */
    public function groupBy(callable $getGroupKey): GroupedCollection;

    /**
     * Returns the first element that passes the given predicate, or just the first element if no predicate is provided.
     * Throws an exception if no matching element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function first(callable $predicate = null);

    /**
     * Returns the first element that passes the given predicate, or just the first element if no predicate is provided.
     * Returns null if no element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function firstOrNull(callable $predicate = null);

    /**
     * Returns the last element that passes the given predicate, or just the last element if no predicate is provided.
     * Throws an exception if no matching element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function last(callable $predicate = null);

    /**
     * Returns the last element that passes the given predicate, or just the last element if no predicate is provided.
     * Returns null if no element is found.
     * @param   callable    $predicate
     * @return  mixed
     */
    public function lastOrNull(callable $predicate = null);

    /**
     * Returns the single element that passes the given predicate, or the single element in the sequence if no predicate is provided
     * @param   callable|null   $predicate
     * @throws  \LogicException If the number of matching elements found is not 1
     * @return  mixed
     */
    public function single(callable $predicate = null);

    /**
     * Returns the single element that passes the given predicate (or the single element in the sequence if no predicate is provided). Returns null if no matching element is found.
     * @param   callable|null   $predicate
     * @throws  \LogicException If the number of matching elements found is greater than 1
     * @return  mixed
     */
    public function singleOrNull(callable $predicate = null);

    /**
     * Materializes this Sequence to an array with numeric keys. Keys from the original iterator may not be preserved, depending on the implementation.
     * @return  array
     */
    public function toArray(): array;

    /**
     * Converts the sequence to a HashSet, using the given hash function (or the default if none is provided)
     * @param   callable|null  $compareFunction
     * @param   callable|null  $hashFunction
     * @return  HashSet
     */
    public function asSet(?callable $compareFunction = null, ?callable $hashFunction = null): HashSet;

    /**
     * Converts the sequence to a Dictionary
     * @param   callable    $keySelector
     * @param   callable|null   $valueSelector Optional
     * @return  Dictionary
     */
    public function toDictionary(callable $keySelector, ?callable $valueSelector = null): Dictionary;

    /**
     * Filters out all elements that exist in the given iterable. The remaining elements are not guaranteed to be distinct.
     * @param   iterable    $items
     * @param   callable|null   $equalityFunction   Leave default for the default used by HashSet
     * @param   callable|null   $hashFunction   Leave default for the default used by HashSet
     * @return  Sequence
     */
    public function except(iterable $items, ?callable $equalityFunction = null, ?callable $hashFunction = null): Sequence;

    /**
     * Filters out all elements that do not exist in the given iterable. The remaining elements are not guaranteed to be distinct.
     * @param   iterable    $items
     * @param   callable|null   $equalityFunction   Leave default for the default used by HashSet
     * @param   callable|null   $hashFunction   Leave default for the default used by HashSet
     * @return  Sequence
     */
    public function intersect(iterable $items, ?callable $equalityFunction = null, ?callable $hashFunction = null): Sequence;

    /**
     * Combine the corresponding elements from two sequences
     * @param   iterable    $seq
     * @param   callable    $mapper
     * @return  Sequence
     */
    public function zip(iterable $seq, callable $mapper): Sequence;

    /**
     * Returns a Sequence of Collections containing at most $count elements
     * @param   int $count
     * @return  Sequence[Collection]
     */
    public function chunk(int $count): Sequence;

    /**
     * Performs an inner join of two Sequences
     * @param   iterable    $rightSequence  The sequence to join with
     * @param   callable    $leftKeySelector    Function selecting the join key from $this
     * @param   callable    $rightKeySelector   Function selecting the join key from $rightSequence
     * @param   callable    $mapResult  Function mapping the joined elements to a result
     * @return  Sequence
     */
    public function join(iterable $rightSequence, callable $leftKeySelector, callable $rightKeySelector, callable $mapResult): Sequence;

    /**
     * Joins the elements of this Sequence with a string.
     * @see \implode
     * @param   string  $glue
     * @return  string
     */
    public function implode(string $glue): string;

    /**
     * Materializes the Sequence to a Collection
     * @return  Collection
     */
    public function collect(): Collection;
}
