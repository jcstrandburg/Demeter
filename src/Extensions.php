<?php
namespace Jcstrandburg\Demeter;

class Extensions
{
    public static function extendSequence(string $methodName, callable $body)
    {
        self::extend(LazySequence::class, $methodName, $body);
    }

    public static function unextendSequence(string $methodName)
    {
        self::unextend(LazySequence::class, $methodName);
    }

    public static function extendCollection(string $methodName, callable $body)
    {
        self::extend(ArrayCollection::class, $methodName, $body);
    }

    public static function unextendCollection(string $methodName)
    {
        self::unextend(ArrayCollection::class, $methodName);
    }

    public static function extendDictionary(string $methodName, callable $body)
    {
        self::extend(ArrayDictionary::class, $methodName, $body);
    }

    public static function unextendDictionary(string $methodName)
    {
        self::unextend(ArrayDictionary::class, $methodName);
    }

    public static function extendSet(string $methodName, callable $body)
    {
        self::extend(HashSet::class, $methodName, $body);
    }

    public static function unextendSet(string $methodName)
    {
        self::unextend(HashSet::class, $methodName);
    }

    public static function extendGroupedCollection(string $methodName, callable $body)
    {
        self::extend(ArrayGroupedCollection::class, $methodName, $body);
    }

    public static function unextendGroupedCollection(string $methodName)
    {
        self::unextend(ArrayGroupedCollection::class, $methodName);
    }

    public static function extendGrouping(string $methodName, callable $body)
    {
        self::extend(ArrayGrouping::class, $methodName, $body);
    }

    public static function unextendGrouping(string $methodName)
    {
        self::unextend(ArrayGrouping::class, $methodName);
    }

    private static function extend($class, string $methodName, callable $body)
    {
        ($class)::extend($methodName, $body);
    }

    private static function unextend($class, string $methodName)
    {
        ($class)::unextend($methodName);
    }
}
