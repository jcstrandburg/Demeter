# Demeter

This library provides a set of collection classes that allow for an consistent, object oriented, fluent style of manipulating data collections. It is mainly inspired by LINQ from C# and the Java Stream API.

### Installing

`composer installl jcstrandburg\demeter`

### Usage

Vanilla PHP:

```php
$x = array_slice(
  array_map(
    function ($x) {
      return $x * 2;
    },
    array_filter([1, 2, 3, 4, 5], function ($x) {return $x % 2 == 1;})),
  0,
  2);
```

With Demeter:

```php
use function Jcstrandburg\Demeter\sequence;

$x = sequence([1, 2, 3, 4, 5])
    ->filter(function($x) {return $x % 2 == 1;})
    ->map(function($x) {return $x * 2;})
    ->take(2);
```

## Version History

### Unreleased

### 0.1

#### Added
* Sequence
* Collection
* Various utility functions
* LazyRewindableIterator
* MappedIterator
* SkipWhileIterator
* TakeWhileIterator

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
