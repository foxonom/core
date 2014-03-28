<?php
namespace Headzoo\Core;

/**
 * Contains static methods for working with strings.
 * 
 * The methods of this class are normalized, in that the string to operate on
 * is always the first argument.
 */
class Strings
    extends Obj
{
    /**
     * Default character set when not defined in the php.ini
     */
    const DEFAULT_CHAR_SET = "ISO-8859-1";
    
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
     * Transform to upper case
     */
    const TR_UPPER = 1;

    /**
     * Transform to lower case
     */
    const TR_LOWER = 2;

    /**
     * Transform the first character of each word in a string, and the rest to lower case
     */
    const TR_TITLE = 3;

    /**
     * Transform the first character in a sentence into uppercase
     */
    const TR_UC_FIRST = 4;

    /**
     * Transform the first character in a sentence to lower case
     */
    const TR_LC_FIRST = 5;

    /**
     * Transform a string from CamelCaseText to underscore_text
     */
    const TR_UNDERSCORE = 6;

    /**
     * Transform a string from underscore_text to CamelCaseText
     */
    const TR_CAMEL_CASE = 7;

    /**
     * Truncate a string at the start
     */
    const TRUNC_START = 1;

    /**
     * Truncate a string in the middle
     */
    const TRUNC_MIDDLE = 2;
    
    /**
     * Truncate a string at the end
     */
    const TRUNC_END = 3;

    /**
     * The character set to use
     * @var string
     */
    private static $char_set = null;

    /**
     * Whether to use the mbstring extension
     * @var bool
     */
    private static $use_mbstring = false;
    
    /**
     * Lower case letters, used to generate random strings
     * @var string
     */
    private static $chars_lower = "abcdefghjkmnopqrstuvwxyz";

    /**
     * Upper case letters, used to generate random strings
     * @var string
     */
    private static $chars_upper = "ABCDEFGHJKMNPQRSTUVWXYZ";

    /**
     * Numbers, used to generate random strings
     * @var string
     */
    private static $chars_numbers = "23456789";

    /**
     * Punctuation, used to generate random strings
     * @var string
     */
    private static $chars_punctuation = "!@#$%^&*()+=-_,.?{}[]<>";

    /**
     * Name of the mbstring extension
     * 
     * Used for testing purposes. The value should not be changed unless testing.
     * 
     * @var string
     */
    public static $__mbstring_extension_name = "mbstring";

    /**
     * Sets whether to use the "mbstring" extension for string operations
     * 
     * The "mbstring" extension must be loaded when set to true. An exception is thrown if the extension
     * is not loaded. When set to false, the mbstring extension will not be used even if the
     * extension is loaded.
     *
     * When turned on with true, the character set used by methods of this class will be set to the return
     * value of ::getDefaultCharacterSet(). Use the ::setCharacterSet() method to set a different character set.
     * See the ::getDefaultCharacterSet() method for more information.
     * 
     * @param bool $use_mbstring Whether to use the multi-byte extension
     * @throws \Headzoo\Core\Exceptions\RuntimeException If the "mbstring" extension is not loaded
     */
    public static function setUseMultiByte($use_mbstring)
    {
        if ($use_mbstring) {
            if (!extension_loaded(self::$__mbstring_extension_name)) {
                self::toss(
                    "RuntimeException",
                    "The '{0}' extension must be enabled.",
                    self::$__mbstring_extension_name
                );
            }
            self::$use_mbstring = true;
            if (!self::$char_set) {
                self::$char_set = self::getDefaultCharacterSet();
            }
        } else {
            self::$use_mbstring = false;
        }
    }
    
    /**
     * Sets the character set this class uses for string operations
     *
     * The value of $char_set must be a valid character set name as defined by mb_list_encodings(), but the
     * name may use any case and omit dashes. For example these are all valid character set
     * names: "UTF-8", "utf-8", "UTF8", "utf8". The name of the encoding will be converted to it's proper
     * name.
     *
     * Note: The return value from ::getDefaultCharSet() will be used when $char_set is empty. See that method
     * for more information.
     *
     * Calling this method with any value other than "ASCII", "ISO-8859-1", empty, or the return value
     * from ::getDefaultCharacterSet() will automatically call ::setUseMultiByte(true) to turn on the use of
     * the "mbstring" extension. See the documentation on ::setUseMultiByte() for more information.
     * 
     * Note: This method does not change php's default internal encoding, eg by calling mb_internal_encoding().
     * It only affects the encoding used when calling methods of this class.
     * 
     * @param string $char_set The name of the character encoding
     * @throws \Headzoo\Core\Exceptions\InvalidArgumentException If $encoding is not a valid encoding name
     * @throws \Headzoo\Core\Exceptions\RuntimeException If the "mbstring" extension is not loaded
     */
    public static function setCharacterSet($char_set)
    {
        $default   = self::getDefaultCharacterSet();
        $char_set  = $char_set ? strtoupper($char_set) : $default;
        $available = array_map("strtoupper", mb_list_encodings());
        $is_valid  = false;
        foreach($available as $a) {
            if ($a === $char_set) {
                $is_valid = true;
                break;
            } else if (str_replace("-", "", $a) === $char_set) {
                $char_set = $a;
                $is_valid = true;
                break;
            }
        }
        if (!$is_valid) {
            self::toss(
                "InvalidArgumentException",
                "Value '{0}' is not a valid character set name.",
                $char_set
            );
        }
        
        $mb_required = ["ASCII", "ISO-8859-1", $default];
        if (!self::$use_mbstring && !in_array($char_set, $mb_required)) {
            self::setUseMultiByte(true);
        }
        
        self::$char_set = $char_set;
    }

    /**
     * Returns the default character set used for string operations
     * 
     * Returns the value of the "default_charset" php.ini directive, or ::DEFAULT_CHAR_SET when
     * php has not been configured to use a default character set.
     * 
     * @return string
     */
    public static function getDefaultCharacterSet()
    {
        $default = ini_get("default_charset");
        return strtoupper($default ? $default : self::DEFAULT_CHAR_SET);
    }
    
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
     * The return value will never contain characters "i", "I", "l", "L", "O", 0 and
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
     * Note: The method also treats dashes "-" as underscores.
     * 
     * Example:
     * ```php
     * echo Strings::camelCaseToUnderscore("CamelCaseString");
     * // Outputs: "camel_case_string"
     *
     * echo Strings::camelCaseToUnderscore("MaryHadALittleLamb");
     * // Outputs: "mary_had_a_little_lamb"
     * ```
     * 
     * @param  string $str The string to transform
     * @return string
     */
    public static function camelCaseToUnderscore($str)
    {
        $under = "";
        $chars = self::chars($str);
        $lower = false;
        $upper = false;
        foreach($chars as $char) {
            if ("_" !== $char && "-" !== $char) {
                if (self::startsUpper($char)) {
                    $under .= ($lower || $upper) ? "_{$char}" : $char;
                    $lower  = false;
                    $upper  = true;
                } else {
                    $under .= $char;
                    $lower  = true;
                    $upper  = false;
                }
            }
        }
        
        return self::toLower($under);
    }

    /**
     * Transforms a string with underscore_text into a string with CamelCaseText
     *
     * Note: The method also treats dashes "-" as underscores.
     * 
     * Example:
     * ```php
     * echo Strings::underscoreToCamelCase("camel_case_string");
     * // Outputs: "CamelCaseString"
     *
     * $str = Strings::underscoreToCamelCase("camel-case-string");
     * // Outputs: "CamelCaseString"
     * ```
     *
     * @param  string $str The string to transform
     * @return string
     */
    public static function underscoreToCamelCase($str)
    {
        $str = self::replace($str, "_", " ");
        $str = self::replace($str, "-", " ");
        $str = self::title($str);
        return self::replace($str, " ", "");
    }

    /**
     * Returns whether the string starts with another string
     * 
     * @param  string $str  The string to search
     * @param  string $find The string to find
     * @return bool
     */
    public static function startsWith($str, $find)
    {
        return self::$use_mbstring
            ? mb_strpos($str, $find) === 0
            : strpos($str, $find) === 0;
    }

    /**
     * Returns whether the string ends with another string
     *
     * @param  string $str  The string to search
     * @param  string $find The string to find
     * @return bool
     */
    public static function endsWith($str, $find)
    {
        if (self::$use_mbstring) {
            $len = mb_strlen($find);
            $is_ends = mb_substr($str, "-{$len}") === $find;
        } else {
            $len = strlen($find);
            $is_ends = substr($str, "-{$len}") === $find;
        }
        
        return $is_ends;
    }

    /**
     * Returns whether the string starts with an upper case character
     * 
     * @param  string $str The string to search
     * @return bool
     */
    public static function startsUpper($str)
    {
        $char = self::sub($str, 0, 1);
        return self::toUpper($char) === $char;
    }

    /**
     * Returns whether the string starts with a lower case character
     *
     * @param  string $str The string to search
     * @return bool
     */
    public static function startsLower($str)
    {
        $char = self::sub($str, 0, 1);
        return self::toLower($char) === $char;
    }
    
    /**
     * Replaces characters in a string
     * 
     * @param  string $str The string to transform
     * @param  string $search The string to find
     * @param  string $replace The replacement string
     * @return string
     */
    public static function replace($str, $search, $replace)
    {
        if (self::$use_mbstring) {
            $search_len  = mb_strlen($search);
            $replace_len = mb_strlen($replace);
            $search_pos  = mb_strpos($str, $search);
            while ($search_pos !== false) {
                $str = mb_substr($str, 0, $search_pos) .
                    $replace .
                    mb_substr($str, $search_pos + $search_len);
                $search_pos = mb_strpos($str, $search, $search_pos + $replace_len);
            }
        } else {
            $str = str_replace($search, $replace, $str);
        }
        
        return $str;
    }

    /**
     * Returns the number of characters in the string
     * 
     * @param  string $str The string to count
     * @return int
     */
    public static function length($str)
    {
        return self::$use_mbstring
            ? mb_strlen($str, self::$char_set)
            : strlen($str);
    }

    /**
     * Splits a string into individual characters
     * 
     * @param  string $str The string to split
     * @return array
     */
    public static function chars($str)
    {
        if (self::$use_mbstring) {
            $chars = [];
            for($i = 0, $l = self::length($str); $i < $l; $i++) {
                $chars[] = mb_substr($str, $i, 1, self::$char_set);
            }
        } else {
            $chars = str_split($str);
        }
        
        return $chars;
    }
    
    /**
     * Transforms a string to upper case
     * 
     * @param  string $str The string to transform
     * @return string
     */
    public static function toUpper($str)
    {
        return self::$use_mbstring
            ? mb_strtoupper($str, self::$char_set)
            : strtoupper($str);
    }

    /**
     * Transforms a string to lower case
     *
     * @param  string $str The string to transform
     * @return string
     */
    public static function toLower($str)
    {
        return self::$use_mbstring
            ? mb_strtolower($str, self::$char_set)
            : strtolower($str);
    }

    /**
     * Transforms the first letter of each word to upper case
     *
     * @param  string $str The string to transform
     * @return string
     */
    public static function ucFirst($str)
    {
        if (self::$use_mbstring) {
            $first = self::toUpper(self::sub($str, 0, 1));
            $str   = $first . self::sub($str, 1);
        } else {
            $str   = ucfirst($str);
        }
        
        return $str;
    }

    /**
     * Transforms the first letter of each word to lower case
     *
     * @param  string $str The string to transform
     * @return string
     */
    public static function lcFirst($str)
    {
        if (self::$use_mbstring) {
            $first = self::toLower(self::sub($str, 0, 1));
            $str   = $first . self::sub($str, 1);
        } else {
            $str   = lcfirst($str);
        }
        
        return $str;
    }

    /**
     * Upper cases the first letter of each word in the string
     *
     * @param  string $str The string to transform
     * @return string
     */
    public static function title($str)
    {
        return self::$use_mbstring
            ? mb_convert_case($str, MB_CASE_TITLE, self::$char_set)
            : ucwords(strtolower($str));
    }

    /**
     * Returns a portion of the string
     *
     * @param  string $str   The string
     * @param  int    $start The start position
     * @param  int    $end   The end position
     * @return string
     */
    public static function sub($str, $start, $end = null)
    {
        // The $end argument must be left out of the function call when it's not used.
        // Passing null to these functions doesn't work.
        if (null !== $end) {
            return self::$use_mbstring
                ? mb_substr($str, $start, $end)
                : substr($str, $start, $end);
        } else {
            return self::$use_mbstring
                ? mb_substr($str, $start)
                : substr($str, $start);
        }
    }

    /**
     * Splits a string by regular expression
     *
     * @param  string $str     The string to split
     * @param  string $pattern The regular expression pattern
     * @param  string $limit   If optional parameter limit is specified, it will be split in limit elements as maximum
     * @return array
     */
    public static function split($str, $pattern, $limit = -1)
    {
        return self::$use_mbstring
            ? mb_split($pattern, $str, $limit)
            : preg_split($pattern, $str, $limit);
    }

    /**
     * Truncates strings which exceed a maximum length
     * 
     * An ellipsis is added to the string to indicate it's been shortened. The final length
     * of the string, including ellipsis, will be the length specified by $max_len.
     * 
     * Strings may be truncated in three places: the start, middle, and end.
     * 
     * Examples:
     * ```php
     * // Truncating a string at the end.
     * echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_END);
     * 
     * // Outputs: "Mary had a little..."
     * 
     * // Truncating a string at the start.
     * echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_START);
     *
     * // Outputs: "...as white as snow."
     *
     * // Truncating a string in the middle.
     * echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_MIDDLE);
     *
     * // Outputs: "Mary ha...e as snow."
     * ```
     * 
     * @param  string $str          The string to truncate
     * @param  int    $max_len      The maximum length
     * @param  int    $pos          Where in the string the cut should be made
     * @param  string $ellipsis     A string which indicates the string was truncated
     * @return string
     */
    public static function truncate($str, $max_len, $pos = self::TRUNC_END, $ellipsis = "...")
    {
        if (self::length($str) > $max_len) {
            $ellipsis_len = self::length($ellipsis);
            switch($pos) {
                case self::TRUNC_START:
                    $max_len -= $ellipsis_len;
                    $str = $ellipsis . self::sub($str, "-{$max_len}");
                    break;
                case self::TRUNC_MIDDLE:
                    $start   = self::sub($str, 0, round($max_len / 2) - $ellipsis_len);
                    $max_len = $max_len - self::length($start) - $ellipsis_len;
                    $end     = self::sub($str, "-{$max_len}");
                    $str     = "{$start}{$ellipsis}{$end}";
                    break;
                case self::TRUNC_END:
                    $max_len -= $ellipsis_len;
                    $str = self::sub($str, 0, $max_len) . $ellipsis;
                    break;
                default:
                    self::toss(
                        "InvalidArgumentException",
                        "Invalid truncate position '{0}'.",
                        $pos
                    );
                    break;
            }
        }
        
        return $str;
    }
    
    /**
     * Transform a string
     *
     * This method wraps the functions ::toLower(), ::toUpper(), ::title(), ::ucFirst(), ::lcFirst(),
     * ::camelCaseToUnderscore(), and ::underscoreToCamelCase()
     * .
     * The notable difference is strings are passed by reference to this method.
     *
     * The value of $transformation must be one of the Strings::TR constants, which map to the following methods:
     *  ::TR_LOWER      - ::toLower().
     *  ::TR_UPPER      - ::toUpper().
     *  ::TR_TITLE      - ::title().
     *  ::TR_UC_FIRST   - ::ucFirst().
     *  ::TR_LC_FIRST   - ::lcFirst().
     *  ::TR_UNDERSCORE - ::camelCaseToUnderscore()
     *  ::TR_CAMEL_CASE - ::underscoreToCamelCase()
     *
     * @param string $str            The string to transform
     * @param int    $transformation The transformation to apply
     * @throws Exceptions\InvalidArgumentException When $transformation is not one of the ::TR constants
     */
    public static function transform(&$str, $transformation = self::TR_LOWER)
    {
        switch($transformation) {
            case self::TR_LOWER:
                $str = self::toLower($str);
                break;
            case self::TR_UPPER:
                $str = self::toUpper($str);
                break;
            case self::TR_TITLE:
                $str = self::title($str);
                break;
            case self::TR_UC_FIRST:
                $str = self::ucFirst($str);
                break;
            case self::TR_LC_FIRST:
                $str = self::lcFirst($str);
                break;
            case self::TR_UNDERSCORE:
                $str = self::camelCaseToUnderscore($str);
                break;
            case self::TR_CAMEL_CASE:
                $str = self::underscoreToCamelCase($str);
                break;
            default:
                self::toss(
                    "InvalidArgumentException",
                    "Transforming argument {0}({1}) must be one of the {me}::TR constants",
                    __METHOD__,
                    $transformation
                );
                break;
        }
    }
}