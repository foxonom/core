<?php
namespace Headzoo\Utilities;

/**
 * Contains static methods for working with functions and methods.
 */
class Functions
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
     * $values = [" headzoo ", " joe "];
     * joinArray($values, "-", "trim");
     * 
     * // The middle argument may be omitted, and called like this:
     * joinArray($values, "trim");
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
} 