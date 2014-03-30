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
     * The user level error constants
     * @var array
     */
    private static $errors_user = [
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,
        E_USER_DEPRECATED
    ];

    /**
     * Returns a boolean value indicating whether an integer is a E_ERROR constant
     * 
     * Given an integer, the method returns true when one of the E_ERROR constants has
     * the same value.
     * 
     * Examples:
     * ```php
     * $is_true = Errors::isTrueError(E_WARNING);
     * var_dump($is_true);
     * // Outputs: bool(true)
     * 
     * $is_true = Errors::isTrueError("E_WARNING");
     * var_dump($is_true);
     * // Outputs: bool(false)
     * ```
     * 
     * @param  int $error The value to test
     *
     * @return bool
     */
    public static function isTrueError($error)
    {
        return in_array((int)$error, self::$errors);
    }

    /**
     * Returns a boolean value indicating whether an integer is one of the E_USER error constants
     * 
     * Given an integer, the method returns true when one of the E_USER_ERROR constants has
     * the same value.
     * 
     * Examples:
     * ```php
     * $is_user = Errors::isTrueUser(E_USER_ERROR);
     * var_dump($is_user);
     * // Outputs: bool(true)
     * 
     * $is_user = Errors::isTrueUser(E_ERROR);
     * var_dump($is_user);
     * // Outputs: bool(false)
     * ```
     * 
     * @param  int|string   $error The error to test
     * 
     * @return bool
     */
    public static function isTrueUser($error)
    {
        return in_array((int)$error, self::$errors_user);
    }

    /**
     * Returns the value of the error as an integer
     * 
     * The value of $error may be either one of the E_ERROR constants, or a string naming one
     * of the constants. The integer value of the constant is returned, or an exception is
     * thrown when $error is not valid.
     *
     * The $error argument may be either an integer (one of the E_ERROR values) or a string
     * with the name of an E_ERROR constant.
     * 
     * Examples:
     * ```php
     * echo Errors::toInteger("E_STRICT");
     * // Outputs: 2048
     * 
     * echo Errors::toInteger(E_WARNING);
     * // Outputs: 2
     * ```
     * 
     * @param  string|int $error The error to convert
     * @throws Exceptions\InvalidArgumentException When $error is not a valid E_ERROR constant
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
     * Returns the string representation of the given error. For example when given an
     * E_WARNING, the method returns "E_WARNING".
     * 
     * The $error argument may be either an integer (one of the E_ERROR values) or a string
     * with the name of an E_ERROR constant.
     * 
     * Examples:
     * ```php
     * echo Errors::toString(E_WARNING);
     * // Outputs: "E_WARNING"
     * 
     * echo Errors::toString("E_CORE_ERROR");
     * // Outputs: "E_CORE_ERROR"
     * ```
     * 
     * @param  int|string $error The error to convert
     *                       
     * @throws Exceptions\InvalidArgumentException When $error is not a valid E_ERROR constant
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
     * Converts an E_ERROR constant to the E_USER_ERROR equivalent
     *
     * For example when given an E_WARNING constant value, the error is converted into
     * an E_USER_WARNING value. Not all E_ERROR constants have E_USER_ERROR equivalents.
     * In those cases the error remains unchanged, and the method returns false.
     * 
     * Returns whether the error is an E_USER_ERROR. Either before or after converting.
     * 
     * Examples:
     * ```php
     * $error = E_ERROR;
     * $is_user = Errors::toUser($error);
     * var_dump($error);
     * var_dump($is_user);
     * 
     * // Outputs:
     * // 256 (E_USER_ERROR)
     * // bool(true)
     * 
     * $error = E_USER_WARNING;
     * $is_user = Errors::toUser($error);
     * var_dump($error);
     * var_dump($is_user);
     * 
     * // Outputs:
     * // 512 (E_USER_WARNING)
     * // bool(true)
     * 
     * $error = E_CORE_ERROR;
     * $is_user = Errors::toUser($error);
     * var_dump($error);
     * var_dump($is_user);
     * 
     * // Outputs:
     * // 16 (E_CORE_ERROR)
     * // bool(false)
     * ```
     * 
     * @param  int $error The error to convert
     * 
     * @return bool
     */
    public static function toUser(&$error)
    {
        $error_int = self::toInteger($error);
        switch($error_int) {
            case E_ERROR:
                $error = E_USER_ERROR;
                break;
            case E_WARNING:
                $error = E_USER_WARNING;
                break;
            case E_NOTICE:
                $error = E_USER_NOTICE;
                break;
            case E_DEPRECATED:
                $error = E_USER_DEPRECATED;
                break;
        }
        
        return self::isTrueUser($error);
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