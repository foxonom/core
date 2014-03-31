<?php
namespace Headzoo\Core;

/**
 * Contains methods for working with functions and methods.
 */
trait FunctionsTrait
{    
    /**
     * Swaps two values when the second is empty and the first is not
     * 
     * Returns true when the arguments were swapped, and false if not.
     * 
     * Example:
     * ```php
     * $optional = "live";
     * $swap     = null;
     * $is_swapped = Functions::swapArgs($optional, $swap, "dev");
     * var_dump($is_swapped);
     * var_dump($optional);
     * var_dump($swap);
     * 
     * // Outputs:
     * // bool(true)
     * // string(4) "dev"
     * // string(4) "live"
     * ```
     * 
     * @param mixed  $optional  Swap when this value is not empty
     * @param mixed  $swap      Swap when this value is empty
     * @param null   $default   The new value for $optional when swapped
     *
     * @return bool
     */
    protected static function swapArgs(&$optional, &$swap, $default = null)
    {
        $is_swapped = false;
        if (empty($swap) && !empty($optional)) {
            $swap       = $optional;
            $optional   = $default;
            $is_swapped = true;
        }
        
        return $is_swapped;
    }
    
    /**
     * Swaps two variables when the second is a callable object
     *
     * Used to create functions/methods which have callbacks as the final argument, and
     * it's desirable to make middle argument optional, while the callback remains the
     * final argument.
     * 
     * Throws an exception when $callable_required is true, and the callable object is
     * empty.
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
     * @param  mixed $optional          The optional argument
     * @param  mixed $callable          Possibly a callable object
     * @param  mixed $default           The optional argument default value
     * @param  bool  $callable_required Whether the callable object is required (cannot be empty)
     * @throws Exceptions\InvalidArgumentException When the callable is required and empty
     * 
     * @return bool
     */
    protected static function swapCallable(&$optional, &$callable, $default = null, $callable_required = true)
    {
        $is_swapped = false;
        if (is_callable($optional)) {
            $callable = $optional;
            $optional = $default;
            $is_swapped  = true;
        }
        if ($callable_required && !$callable) {
            throw new Exceptions\InvalidArgumentException(
                "A callable object is required"
            );
        }
        
        return $is_swapped;
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
    protected static function validateRequired(array $values, array $required, $allow_empty = false)
    {
        if (!$allow_empty) {
            $values = array_filter($values);
        }
        $missing = array_diff($required, array_keys($values));
        if (!empty($missing)) {
            throw new Exceptions\ValidationFailedException(
                sprintf(
                    "Required values missing: %s.",
                    Arrays::conjunct($missing, 'Headzoo\Core\Strings::quote')
                )
            );
        }

        return true;
    }
} 