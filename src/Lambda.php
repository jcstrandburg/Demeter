<?php
namespace Jcstrandburg\Demeter;

class Lambda
{
    public static function isOdd(): callable
    {
        return function ($x) {
            return $x % 2 == 1;
        };
    }

    public static function isEven(): callable
    {
        return function ($x) {
            return $x % 2 == 0;
        };
    }

    public static function isEqualTo($y): callable
    {
        return function ($x) use ($y) {
            return $x == $y;
        };
    }

    public static function areEqual(): callable
    {
        return function ($a, $b) {
            return $a == $b;
        };
    }

    public static function isStrictlyEqualTo($y): callable
    {
        return function ($x) use ($y) {
            return $x === $y;
        };
    }

    public static function areStrictlyEqual(): callable
    {
        return function ($a, $b) {
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
        return function ($x) use ($y) {
            return $x > $y;
        };
    }

    public static function isGreaterThanOrEquall($y): callable
    {
        return function ($x) use ($y) {
            return $x >= $y;
        };
    }

    public static function isLessThan($y): callable
    {
        return function ($x) use ($y) {
            return $x < $y;
        };
    }

    public static function isLessThanOrEquall($y): callable
    {
        return function ($x) use ($y) {
            return $x <= $y;
        };
    }
}
