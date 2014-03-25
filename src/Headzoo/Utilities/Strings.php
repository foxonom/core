<?php
namespace Headzoo\Utilities;

/**
 * Contains static methods for working with strings.
 */
class Strings
{
    /**
     * Use lower case letters in random strings
     */
    const CHARS_LOWER = 0x1;

    /**
     * Use upper case letters in random strings
     */
    const CHARS_UPPER = 0x2;

    /**
     * Use numbers in random strings
     */
    const CHARS_NUMBERS  = 0x4;

    /**
     * Use punctuation in random strings
     */
    const CHARS_PUNCTUATION  = 0x8;

    /**
     * Lower case letters, used to generate random strings
     * @var string
     */
    private static $chars_lower = "abcdefghjkmnopqrstuvwxyz";

    /**
     * Upper case letters, used to generate random strings
     * @var string
     */
    private static $chars_upper = "ABCDEFGHJKMNOPQRSTUVWXYZ";

    /**
     * Numbers, used to generate random strings
     * @var string
     */
    private static $chars_numbers = "023456789";

    /**
     * Punctuation, used to generate random strings
     * @var string
     */
    private static $chars_punctuation = "!@#$%^&*()+=-_,.?{}[]<>";

    /**
     * Simply wraps a string in quote characters
     * 
     * Useful as a callback function to functions like array_map(), this method wraps a string in your choose of
     * quote character.
     * 
     * Note: Quote characters found in the string are not escaped with slashes.
     * 
     * Example:
     * ```php
     * $str = "Mary had a little lamb.";
     * echo Strings::quote($str);
     * // Outputs: 'Mary had a little lamb.'
     * 
     * echo Strings::quote($str, "`");
     * // Outputs: `Mary had a little lamb.`
     * ```
     * 
     * @param  string $str   The string to quote
     * @param  string $quote The quote character to use
     * @return string
     */
    public static function quote($str, $quote = "'")
    {
        return "{$quote}{$str}{$quote}";
    }
    
    /**
     * Returns a completely random string $len characters long
     *
     * The $char_class argument controls which class of characters will be used
     * in the random string: lower case letters, upper case letters, numbers,
     * and punctuation. Use the self::CHARS constants and bitwise OR to
     * specify several character classes.
     *
     * Example:
     * ```php
     * Strings::random(10, Strings::CHARS_LOWER | Strings::CHARS_UPPER);
     * ```
     *
     * The return value will container lower case and upper case letters. By
     * default the character class used is
     * (Strings::CHARS_LOWER | Strings::CHARS_UPPER | Strings::CHARS_NUMBERS),
     * which is every character except punctuation.
     *
     * The return value will never contain characters "i", "I", "l", "L", and
     * "1".
     *
     * @param  int $len        Length of the string to return
     * @param  int $char_class Class of characters to use
     * @return string
     */
    public static function random($len, $char_class = null)
    {
        if (null === $char_class) {
            $char_class = self::CHARS_LOWER | self::CHARS_UPPER | self::CHARS_NUMBERS;
        }

        $chars = null;
        if ($char_class & self::CHARS_LOWER) {
            $chars .= self::$chars_lower;
        }
        if ($char_class & self::CHARS_UPPER) {
            $chars .= self::$chars_upper;
        }
        if ($char_class & self::CHARS_NUMBERS) {
            $chars .= self::$chars_numbers;
        }
        if ($char_class & self::CHARS_PUNCTUATION) {
            $chars .= self::$chars_punctuation;
        }

        $char_count = strlen($chars) - 1;
        $str        = null;
        for($i = 0; $i < $len; $i++) {
            $rand = mt_rand(0, $char_count);
            $str .= $chars[$rand];
        }

        return $str;
    }

    /**
     * Transforms a string with CamelCaseText into a string with underscore_text
     * 
     * Example:
     * ```php
     * $str = "CamelCaseString";
     * $str = Strings::regexCamelCaseToUnderscore($str);
     * var_dum($str);
     * 
     * // Outputs:
     * // string(17) "camel_case_string"
     * ```
     * 
     * @param  string $str The string to transform
     * @return string
     */
    public static function transformCamelCaseToUnderscore($str)
    {
        return strtolower(preg_replace("/([a-z])([A-Z])/", "\\1_\\2", $str));
    }

    /**
     * Transforms a string with underscore_text into a string with CamelCaseText
     *
     * Example:
     * ```php
     * $str = "camel_case_string";
     * $str = Strings::regexCamelCaseToUnderscore($str);
     * var_dum($str);
     *
     * // Outputs:
     * // string(15) "CamelCaseString"
     * ```
     *
     * @param  string $str The string to transform
     * @return string
     */
    public static function transformUnderscoreToCamelCase($str)
    {
        $str = str_replace("_", " ", $str);
        $str = ucwords(strtolower($str));
        return str_replace(" ", "", $str);
    }
}