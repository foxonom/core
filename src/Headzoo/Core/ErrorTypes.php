<?php
namespace Headzoo\Core;

/**
 * Utility class used to work with PHP's E_ error constants.
 */
class ErrorTypes
    extends Obj
{
    /**
     * The error constants
     * @var array
     */
    private static $errors = [
        E_ERROR,
        E_WARNING,
        E_PARSE,
        E_NOTICE,
        E_CORE_ERROR,
        E_CORE_WARNING,
        E_COMPILE_ERROR,
        E_COMPILE_WARNING,
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,
        E_STRICT,
        E_RECOVERABLE_ERROR,
        E_DEPRECATED,
        E_USER_DEPRECATED,
        E_ALL
    ];

    /**
     * Returns a boolean value indicating whether $value is a valid PHP error type
     * 
     * @param  int $type The value to test
     *
     * @return bool
     */
    public static function isValid($type)
    {
        return in_array((int)$type, self::$errors);
    }

    /**
     * Returns the type value
     * 
     * The value of $type may be either one of the E_ error constants, or a string naming one
     * of the constants. The integer value of the constant is returned, or an exception is
     * thrown when $type is not valid.
     * 
     * @param  string|int $type E_ type constant or string with name of constant
     *
     * @throws Exceptions\InvalidArgumentException When $type is not a valid E_ error constant
     *
     * @return int
     */
    public static function getValue($type)
    {
        if (is_string($type) && substr($type, 0, 2) == "E_") {
            $type = @constant($type);
        }
        if (!self::isValid($type)) {
            self::toss(
                "InvalidArgument",
                "The value {0} is not a valid E_ error constant value or name.",
                $type
            );
        }
        
        return $type;
    }

    /**
     * Returns an array of the PHP error types
     * 
     * @return int[]
     */
    public static function types()
    {
        return self::$errors;
    }
} 