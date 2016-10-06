<?php

namespace DoSomething\Gateway\Common;

class Introspector
{
    /**
     * Returns all traits used by a class, its subclasses and trait of their traits.
     * @see illuminate/support's `class_uses_recursive`
     *
     * @param  object|string  $class
     * @return array
     */
    public static function getAllClassTraits($class)
    {
        $results = [];

        foreach (array_merge([$class => $class], class_parents($class)) as $class) {
            $results += self::getAllTraits($class);
        }

        return array_unique($results);
    }

    /**
     * Get the base name of a namespaced class string/object.
     * @see illuminate/support's `class_basename`
     *
     * @param $class
     * @return string
     */
    public static function baseName($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    /**
     * Returns all traits used by a trait and its traits.
     * @see illuminate/support's `trait_uses_recursive`
     *
     * @param  string  $trait
     * @return array
     */
    public static function getAllTraits($trait)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}
