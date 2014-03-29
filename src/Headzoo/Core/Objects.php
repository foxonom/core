<?php
namespace Headzoo\Core;

/**
 * Contains static methods for working with objects and classes.
 */
class Objects
    extends Obj
{
    /**
     * Returns a fully qualified class name
     *
     * Returns a string containing the fully qualified name of the class. The name is normalized by
     * removing leading and trailing namespace separators.
     *
     * The $obj argument may be either an object, or a string. When given a string the value *shout*
     * be the name of a class, but this method does not check if the class exists.
     *
     * Examples:
     * ```php
     * echo Objects::getFullName(new stdClass());
     * // Outputs: "stdClass"
     *
     * echo Objects::getFullName('\Headzoo\Core\Objects');
     * // Outputs: "Headzoo\Core\Objects"
     * ```
     *
     * @param  object|string $obj An object or class name
     * @return string
     */
    public static function getFullName($obj)
    {
        if (is_object($obj)) {
            return get_class($obj);
        }
        return trim($obj, '\\');
    }

    /**
     * Returns whether a value is an object, or array of objects
     *
     * Returns a boolean value indicating whether the $obj argument is an object, or an array of nothing
     * but objects. Returns false when $obj is neither an object nor array.
     *
     * Examples:
     * ```php
     * $obj = new stdClass();
     * $is = Objects::isObject($obj);
     * var_dump($is);
     *
     * // Outputs: bool(true)
     *
     * $objs = [
     *      new stdClass(),
     *      new Headzoo\Core\Strings()
     * ];
     * $is = Objects::isObject($objs);
     * var_dump($is);
     *
     * // Outputs: bool(true)
     *
     * $is = Objects::isObject('stdClass');
     * var_dump($is);
     *
     * // Outputs: bool(false)
     *
     * $objs = [
     *      new stdClass(),
     *      'Headzoo\Core\Strings'
     * ];
     * $is = Objects::isObject($objs);
     * var_dump($is);
     *
     * // Outputs: bool(false)
     * ```
     *
     * @param  object|array $obj The object or array of objects to test
     * @return bool
     */
    public static function isObject($obj)
    {
        if (is_object($obj)) {
            $is_object = true;
        } else if (is_array($obj)) {
            $reduced = array_filter($obj, function($o) {
                    return is_object($o);
                });
            $is_object = count($reduced) == count($obj);
        } else {
            $is_object = false;
        }

        return $is_object;
    }
    
    /**
     * Returns whether the object, or array of objects, is an instance of a class
     * 
     * Similar to PHP's own instanceof comparison operator, this method differs in two ways:
     *  - The first argument may be an array of objects to test.
     *  - The second argument may be a string with the name of a class.
     * 
     * Throws an exception when $obj is not an object or an array of objects.
     * 
     * Examples:
     * ```php
     * $is = Objects::isInstance(new Strings(), new Strings());
     * var_dump($is);
     * // Outputs: bool(true)
     * 
     * $is = Objects::isInstance(new Strings(), Strings::class);
     * var_dump($is);
     * // Outputs: bool(true)
     * 
     * $is = Objects::isInstance(new Arrays(), new Strings());
     * var_dump($is);
     * // Outputs: bool(false)
     * 
     * $is = Objects::isInstance(new Arrays(), Arrays::class);
     * var_dump($is);
     * // Outputs: bool(false)
     * 
     * $objects = [
     *      new Strings(),
     *      new Strings()
     * ];
     * $is = Objects::isInstance($objects, Strings::class);
     * var_dump($is);
     * // Outputs: bool(true)
     * 
     * $objects = [
     *      new Strings(),
     *      new stdClass()
     * ];
     * $is = Objects::isInstance($objects, Strings::class);
     * var_dump($is);
     * // Outputs: bool(false)
     * 
     * $objects = [
     *      [
     *          new Strings(),
     *          new Strings()
     *      ],
     *      [
     *          new Strings(),
     *          new Strings()
     *      ]
     * ];
     * $is = Objects::isInstance($objects, Strings::class);
     * var_dump($is);
     * // Outputs: bool(true)
     * ```
     * 
     * @param  object|object[] $obj   The object or array of objects to test
     * @param  object|string   $class Object or string naming a class
     * @return bool
     * @throws Exceptions\LogicException When the $obj argument is not an object or array of objects
     */
    public static function isInstance($obj, $class)
    {
        if (is_array($obj)) {
            $reduced = array_filter($obj, function($o) use($class) {
                return self::isInstance($o, $class);
            });
            $is_instance = count($obj) == count($reduced);
        } else {
            if (!is_object($obj)) {
                self::toss(
                    "Logic",
                    "Argument 1 must be an object or array. Got type {0}.",
                    gettype($obj)
                );
            }
            
            $class = self::getFullName($class);
            $is_instance = is_subclass_of($obj, $class) ||
                self::getFullName($obj) === $class;
        }
        
        return $is_instance;
    }

    /**
     * Returns whether two objects are equal to each other
     * 
     * For two objects to be equal they must be of the same class type, and public properties from each
     * must have the same value. When $deep is true, the protected and private property values must
     * also be equal. A strict === comparison is done between property values.
     * 
     * Examples:
     * ```php
     * $obj_a = new Strings();
     * $obj_b = new Strings();
     * $obj_a->value = "monkey";
     * $obj_b->value = "moneky";
     * $is_equal = Objects::equals($obj_a, $obj_b);
     * var_dump($is_equal);
     * 
     * // Outputs: bool(true)
     *
     * $obj_a = new Strings();
     * $obj_b = new Strings();
     * $obj_a->value = "monkey";
     * $obj_b->value = "donkey";
     * $is_equal = Objects::equals($obj_a, $obj_b);
     * var_dump($is_equal);
     * 
     * // Outputs: bool(false)
     * ```
     * 
     * @param  object $obj_a The first object to test
     * @param  object $obj_b The second object to test
     * @param  bool   $deep  True to check both public and private properties
     * @return bool
     */
    public static function equals($obj_a, $obj_b, $deep = false)
    {
        $is_equal = false;
        if ($obj_a instanceof $obj_b) {
            if ($deep) {
                $obj_a = ((array)$obj_a);
                $obj_b = ((array)$obj_b);
            } else {
                $obj_a = get_object_vars($obj_a);
                $obj_b = get_object_vars($obj_b);
            }
            $is_equal = $obj_a === $obj_b;
        }
        
        return $is_equal;
    }

    /**
     * Merges two or more objects
     *
     * Copies the public property values from $obj2 to $obj1, and returns $obj1. Properties from $obj2
     * are either added to $obj1, or overwrite existing properties with the same name. Null values
     * do not overwrite non-null values. More than two objects may be merged.
     * 
     * Protected and private properties are not merged.
     * 
     * Examples:
     * ```php
     * $obj_a = new stdClass();
     * $obj_a->job = "Circus Freak";
     * 
     * $obj_b = new stdClass();
     * $obj_b->name = "Joe"
     * $obj_b->job  = "Space Buccaneer";
     * 
     * $obj_c = new stdClass();
     * $obj_c->age = 23;
     * 
     * $merged = Objects::merge($obj_a, $obj_b, $obj_c);
     * var_dump($obj_a === $merged);
     * var_dump($merged);
     * 
     * // Outputs:
     * // bool(true);
     * // class stdClass {
     * //   public $name => string(4) "Joe"
     * //   public $job  => string(15) "Space Buccaneer"
     * //   public $age  => int(23)
     * //   
     * // }
     * 
     * // The objects do not have to be the same type.
     * $obj_a = new Strings();
     * $obj_a->value = "PhpStorm Rocks!";
     * 
     * $obj_b = new Strings();
     * $obj_b->value = "So does IntelliJ IDEA!";
     * 
     * $obj_c = new stdClass();
     * $obj_c->website = "http://www.jetbrains.com/";
     * 
     * $merged = Objects::merge($obj_a, $obj_b, $obj_c);
     * var_dump($merged === $obj_a);
     * var_dump($merged);
     * 
     * // Outputs:
     * // bool(true)
     * // class Strings {
     * //   public $value   => string(22) "So does IntelliJ IDEA!",
     * //   public $website => string(25) "http://www.jetbrains.com/"
     * // }
     * 
     * // Easily merge objects into a new "blank" object.
     * $obj_a = new Strings();
     * $obj_a->value = "PhpStorm Rocks!";
     * 
     * $obj_b = new Arrays();
     * $obj_b->count = 23;
     * 
     * $merged = Objects::merge(new stdClass(), $obj_a, $obj_b);
     * var_dump($merged);
     * 
     * // Outputs:
     * // class stdClass {
     * //   public $value => string(15) "PhpStorm Rocks!",
     * //   public $count => 23
     * // }
     * ```
     * 
     * @param  object $obj  The base object
     * @param  object $obj2 Gets merged into the base object
     * @param  object ...   Additional objects to merge
     * @return mixed
     */
    public static function merge($obj, $obj2)
    {
        $objects = func_get_args();
        $obj     = array_shift($objects);
        if (!is_object($obj)) {
            self::toss(
                "InvalidArgument",
                "Merged values must be objects. Got type {0}.",
                gettype($obj)
            );
        }
        
        foreach($objects as $o) {
            if (!is_object($o)) {
                self::toss(
                    "InvalidArgument",
                    "Merged values must be objects. Got type {0}.",
                    gettype($o)
                );
            }
            foreach($o as $name => $value) {
                if (null !== $value) {
                    $obj->$name = $value;
                }
            }
        }
        
        return $obj;
    }
} 