<?php
namespace Headzoo\Core;

/**
 * Utility class used to work with E_ERROR constants.
 */
class Errors
    extends Obj
{
    /**
     * The error constants
     * @var array
     */
    private static $errors = [
        "E_ERROR"             => E_ERROR,
        "E_WARNING"           => E_WARNING,
        "E_PARSE"             => E_PARSE,
        "E_NOTICE"            => E_NOTICE,
        "E_CORE_ERROR"        => E_CORE_ERROR,
        "E_CORE_WARNING"      => E_CORE_WARNING,
        "E_COMPILE_ERROR"     => E_COMPILE_ERROR,
        "E_COMPILE_WARNING"   => E_COMPILE_WARNING,
        "E_USER_ERROR"        => E_USER_ERROR,
        "E_USER_WARNING"      => E_USER_WARNING,
        "E_USER_NOTICE"       => E_USER_NOTICE,
        "E_STRICT"            => E_STRICT,
        "E_RECOVERABLE_ERROR" => E_RECOVERABLE_ERROR,
        "E_DEPRECATED"        => E_DEPRECATED,
        "E_USER_DEPRECATED"   => E_USER_DEPRECATED,
        "E_ALL"               => E_ALL
    ];

    /**
     * Returns a boolean value indicating whether a value is a E_ERROR constant
     * 
     * @param  int $error The value to test
     *
     * @return bool
     */
    public static function isError($error)
    {
        return in_array((int)$error, self::$errors);
    }

    /**
     * Returns the value of the error as an integer
     * 
     * The value of $error may be either one of the E_ERROR constants, or a string naming one
     * of the constants. The integer value of the constant is returned, or an exception is
     * thrown when $error is not valid.
     * 
     * @param  string|int $error E_ERROR constant or string with name of constant
     * @throws Exceptions\InvalidArgumentException When $error is not a valid E_ error constant
     *
     * @return int
     */
    public static function toInteger($error)
    {
        if (is_string($error) && isset(self::$errors[$error])) {
            $error = self::$errors[$error];
        }
        if (!is_int($error) || !in_array($error, self::$errors)) {
            self::toss(
                "InvalidArgument",
                "The value {0} is not a valid E_ERROR constant value.",
                $error
            );
        }
        
        return $error;
    }

    /**
     * Returns the value of the error as a string
     * 
     * @param $error
     *
     * @return int
     */
    public static function toString($error)
    {
        if (is_int($error) && in_array($error, self::$errors)) {
            $error = array_search($error, self::$errors);
        }
        if (!is_string($error) || !isset(self::$errors[$error])) {
            self::toss(
                "InvalidArgument",
                "The value {0} is not a valid E_ERROR constant value.",
                $error
            );
        }
        
        return $error;
    }

    /**
     * Returns an array of the E_ERROR constant values
     * 
     * @return int[]
     */
    public static function toArray()
    {
        return self::$errors;
    }
} 