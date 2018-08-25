# Demeter

This library provides a set of immutable collection classes that allow for an consistent, object oriented, fluent style of manipulating data collections. It is mainly inspired by LINQ from C# and the Java Stream API.

### Installing

`composer require jcstrandburg\demeter`

### Usage

Vanilla PHP:

```php
$x = array_slice(
  array_map(
    function ($x) { return $x * 2; },
    array_filter([1, 2, 3, 4, 5], function ($x) {return $x % 2 == 1;})),
  0, 2);
```

With Demeter:

```php
use function Jcstrandburg\Demeter\sequence;
use Jcstrandburg\Demeter\Lambda;

$x = sequence([1, 2, 3, 4, 5])
    ->filter(Lambda::isOdd())
    ->map(Lambda::multiplyBy(2))
    ->take(2);
```

## Version History

### Unreleased

### 0.5

#### Changed
* Breaking: Changed the behavior of `HashSet` so that it acts like a proper set (hashing is used for buckets but not equality comparisons)
* Breaking: Convert `Sequence` to an interface, with the existing implementation becoming `LazySequence`
* Breaking: Convert `Collection` to an interface, with the existing implementation becoming `ArrayCollection`
* Breaking: Convert `Dictionary` to an interface, with the existing implementation becoming `ArrayDictionary`
* Breaking: Functions previously returning `HashSet` now return `Set`

#### Added
* Introduce `Set` interface which `HashSet` implements
* `Sequence::except` and `Sequence::intersect`
* `Lambda` utility class
* `dictionary` and `set` factory functions

### 0.4

#### Added
* `HashMap::addMany`
* `HashMap::removeMany`
* `Sequence::toDictionary`
* `Dictionary`

### 0.3

#### Changed
* `sequence` and `collect` now will return their argument unmodified if is already of the correct type

#### Added
* `HashSet`
* `Sequence::asSet`
* `Sequence::first`
* `Sequence::firstOrNull`
* `Sequence::last`
* `Sequence::lastOrNull`
* `Sequence::single`
* `Sequence::singleOrNull`

### 0.2

#### Added
* `Sequence::groupBy`
* `GroupedCollection`
* `Grouping`

### 0.1

#### Added
* `Sequence`
* `Collection`
* Various utility functions
* `LazyRewindableIterator`
* `MappedIterator`
* `SkipWhileIterator`
* `TakeWhileIterator`

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
