<?php
namespace Headzoo\Core;

/**
 * Utility class used to work with PHP's E_ error constants.
 */
class ErrorTypes
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
     * Returns an array of the PHP error types
     * 
     * @return int[]
     */
    public static function types()
    {
        return self::$errors;
    }
} 