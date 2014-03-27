<?php
namespace Headzoo\Core;

/**
 * Contains static methods for working with functions and methods.
 */
class Functions
    extends Obj
{
    /**
     * Swaps two variables when the second is a callable object
     *
     * Used to create functions/methods which have callbacks as the final argument, and
     * it's desirable to make middle argument optional, while the callback remains the
     * final argument.
     * 
     * Returns true if the arguments were swapped, false if not.
     * 
     * Examples:
     * ```php
     * function joinArray(array $values, $separator, callable $callback = null)
     * {
     *      Functions::swapCallable($separator, $callback, "-");
     *      $values = array_map($callback, $values);
     *      return join($separator, $values);
     * }
     * 
     * // The function above may be called normally, like this:
     * $values = ["headzoo", "joe"];
     * joinArray($values, "-", 'Headzoo\Core\String::quote');
     * 
     * // Or the middle argument may be omitted, and called like this:
     * joinArray($values, 'Headzoo\Core\String::quote');
     * ```
     * 
     * @param  mixed $optional The optional argument
     * @param  mixed $callable Possibly a callable object
     * @param  mixed $default  The optional argument default value
     * @return bool
     */
    public static function swapCallable(&$optional, &$callable, $default = null)
    {
        $swapped = false;
        if (is_callable($optional)) {
            $callable = $optional;
            $optional = $default;
            $swapped  = true;
        }
        
        return $swapped;
    }

    /**
     * Throws an exception when required values are missing from an array of key/value pairs
     *
     * The $values argument is an array of key/value pairs, and the $required argument is an array
     * of keys which must exist in $values to validate. When $allow_empty is false, the required values
     * must also evaluate to a non-empty value to validate.
     *
     * This method always returns true, but throws an exception when the value is invalid.
     *
     * @param  array $values        The values to validate
     * @param  array $required      List of keys
     * @param  bool  $allow_empty   Are empty values acceptable?
     * @return bool
     * @throws Exceptions\ValidationFailedException When a required value is missing
     */
    public static function validateRequired(array $values, array $required, $allow_empty = false)
    {
        if (!$allow_empty) {
            $values = array_filter($values);
        }
        $missing = array_diff($required, array_keys($values));
        if (!empty($missing)) {
            self::toss(
                "ValidationFailedException",
                "Required values missing: {0}.",
                Arrays::conjunct($missing, 'Headzoo\Core\Strings::quote')
            );
        }

        return true;
    }
} 