<?php
namespace Jcstrandburg\Demeter;

class Lambda
{
    public static function isOdd(): callable
    {
        return function ($x): bool {
            return $x % 2 == 1;
        };
    }

    public static function isEven(): callable
    {
        return function ($x): bool {
            return $x % 2 == 0;
        };
    }

    public static function isEqualTo($y): callable
    {
        return function ($x) use ($y): bool {
            return $x == $y;
        };
    }

    public static function areEqual(): callable
    {
        return function ($a, $b): bool {
            return $a == $b;
        };
    }

    public static function isStrictlyEqualTo($y): callable
    {
        return function ($x) use ($y): bool {
            return $x === $y;
        };
    }

    public static function areStrictlyEqual(): callable
    {
        return function ($a, $b): bool {
            return $a === $b;
        };
    }

    public static function plus($y): callable
    {
        return function ($x) use ($y) {
            return $x + $y;
        };
    }

    public static function add(): callable
    {
        return function ($a, $b) {
            return $a + $b;
        };
    }

    public static function multiply(): callable
    {
        return function ($a, $b) {
            return $a * $b;
        };
    }

    public static function multiplyBy($y): callable
    {
        return function ($x) use ($y) {
            return $x * $y;
        };
    }

    public static function divideBy($y): callable
    {
        return function ($x) use ($y) {
            return $x / $y;
        };
    }

    public static function identity(): callable
    {
        return function ($x) {
            return $x;
        };
    }

    public static function constant($x): callable
    {
        return function () use ($x) {
            return $x;
        };
    }

    public static function selectKey($y): callable
    {
        return function ($x) use ($y) {
            return $x[$y];
        };
    }

    public static function selectProperty($y): callable
    {
        return function ($x) use ($y) {
            return $x->$y;
        };
    }

    public static function isGreaterThan($y): callable
    {
        return function ($x) use ($y): bool {
            return $x > $y;
        };
    }

    public static function isGreaterThanOrEquall($y): callable
    {
        return function ($x) use ($y): bool {
            return $x >= $y;
        };
    }

    public static function isLessThan($y): callable
    {
        return function ($x) use ($y): bool {
            return $x < $y;
        };
    }

    public static function isLessThanOrEquall($y): callable
    {
        return function ($x) use ($y): bool {
            return $x <= $y;
        };
    }

    public static function getOffSet($array): callable
    {
        if (!is_array($array) && !($array instanceof \ArrayAccess)) {
            throw new \ArgumentException("\$array must be of type 'array' or '\ArrayAccess'");
        }

        return function ($x) use ($array) {
            return $array[$x];
        };
    }

    public static function setContains(Set $set)
    {
        return function ($x) use ($set): bool {
            return $set->contains($x);
        };
    }

    public static function setDoesNotContain(Set $set)
    {
        return function ($x) use ($set): bool {
            return !$set->contains($x);
        };
    }

    public static function toArray()
    {
        return function ($x): array{
            return $x->toArray();
        };
    }

    public static function getGroupKey()
    {
        return function ($x) {
            return $x->getGroupKey();
        };
    }
}
