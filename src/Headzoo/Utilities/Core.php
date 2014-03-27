<?php
namespace Headzoo\Utilities;

/**
 * Base class for core utility classes.
 */
abstract class Core
{
    /**
     * Returns the name of the class
     * 
     * @return string
     */
    public function className()
    {
        return get_called_class();    
    }
    
    /**
     * Throws the configured validation exception
     *
     * Available place holders:
     *  {me}        - The name of the class throwing the exception
     *  {exception} - The name of the exception being thrown
     *  {code}      - The exception code
     *  {date}      - The date the exception was thrown
     * 
     * Examples:
     * ```php
     * $validator = new Validator();
     * $validator->throwException("There was a serious site error!");
     * $validator->throwException("There was a serious site error!", 666);
     * $validator->throwException("There was a {0} {1} error!", 666, "serious", "site");
     *
     * // The middle argument may be omitted when the next argument is not an integer.
     * $validator->throwException("There was a {0} {1} error!", "serious", "site");
     * ```
     *
     * @param string $exception The name of the exception to throw
     * @param string $message   The error message
     * @param int    $code      The error code, defaults to 0
     * @param ...    $args      One or more values to quote into the message
     */
    protected static function toss(/** @noinspection PhpUnusedParameterInspection */ $exception, $message, $code = 0)
    {
        $args = array_values(get_defined_vars());
        foreach(func_get_args() as $i => $value) {
            $args[$i] = $value;
        }
        list($exception, $message, $code) = array_splice($args, 0, 3);
        $exception = __NAMESPACE__ . "\\Exceptions\\{$exception}";
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