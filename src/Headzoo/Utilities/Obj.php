<?php
namespace Headzoo\Utilities;

/**
 * Base class for core utility classes.
 */
abstract class Obj
{
    /**
     * Returns the name of the class
     * 
     * @return string
     */
    public function getClassName()
    {
        return get_called_class();    
    }

    /**
     * Returns the name of the class namespace
     * 
     * The namespace will not have a leading forward-slash, eg "Headzoo\Utilities" instead
     * of "\Headzoo\Utilities". An empty string is returned when the class is in the
     * global namespace.
     * 
     * @return string
     */
    public function getNamespaceName()
    {
        $caller = get_called_class();
        $parts  = explode('\\', $caller);
        array_pop($parts);
        
        return join('\\', $parts);
    }
    
    /**
     * Throws an exception from the calling class namespace
     *
     * Examples:
     * ```php
     * // If the calling class namespace is Headzoo\Utilities this call will throw an
     * // instance of Headzoo\Utilities\Exceptions\InvalidArgumentException with the
     * // given message.
     * $this->toss("InvalidArgumentException", "There was an error.");
     * 
     * // Additional context arguments may be passed to the method which will be interpolated
     * // into the message. The interpolater looks for numerically indexed place holders,
     * // eg {0}, {1}, etc, which map to the extra arguments. This means the context arguments
     * // may be given in any order.
     * $this->toss("RuntimeException", "The {0} system broke.", "database");
     * 
     * // The context interpolater has a few built-in place holders. The "{me}" place holder
     * // will be replaced with the name of the class which threw the exception. Additional
     * // context arguments are inserted into the message per their index.
     * $this->toss("RuntimeException", "The {me} class reported a {0} error.", "serious");
     * 
     * // When the first argument after the message is an integer, it will be used as the
     * // exception code. This call will throw an instance of
     * // Headzoo\Utilities\Exceptions\RuntimeException with the message "There was an error",
     * // and the error code 43.
     * $this->toss("RuntimeException", "There was an error.", 43);
     * 
     * // This call is giving an exception code, and context arguments for interpolation.
     * // Remember when the first argument after the message is an integer, it's treated as
     * // the error code. When you need a number to be interpolated into the message, cast
     * // it to a string.
     * $this->toss("RuntimeException", "There was a {0} error", 43, "database");
     * ```
     *
     * The built in place holders:
     *  {me}        - The name of the class throwing the exception
     *  {exception} - The name of the exception being thrown
     *  {code}      - The exception code
     *  {date}      - The date the exception was thrown
     * 
     * @param string $exception The name of the exception to throw
     * @param string $message   The error message
     * @param int    $code      The error code, defaults to 0
     * @param ...    $args      One or more context arguments to interpolate into the message
     */
    protected static function toss(/** @noinspection PhpUnusedParameterInspection */ $exception, $message, $code = 0)
    {
        $args = array_values(get_defined_vars());
        foreach(func_get_args() as $i => $value) {
            $args[$i] = $value;
        }
        list($exception, $message, $code) = array_splice($args, 0, 3);
        $exception = self::getNamespaceName() . "\\Exceptions\\{$exception}";
        
        if (!is_int($code)) {
            array_unshift($args, $code);
            $code = 0;
        }

        $placeholders = array_merge($args, [
            "me"        => get_called_class(),
            "exception" => $exception,
            "code"      => $code,
            "date"      => date("Y-m-d H:i:s")
        ]);
        $message = self::interpolate($message, $placeholders);
        
        throw new $exception(
            $message,
            $code
        );
    }

    /**
     * Interpolates context values into the message placeholders.
     * Taken from PSR-3's example implementation.
     * 
     * @param  string $message Message with placeholders
     * @param  array  $context Values to replace in the message
     * @return string
     */
    private static function interpolate($message, array $context = []) {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        
        return strtr($message, $replace);
    }
} 