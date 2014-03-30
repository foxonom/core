<?php
namespace Headzoo\Core;

/**
 * Utility class for converting from one value to another.
 */
class Conversions
{
    /**
     * Number of seconds in a minute
     */
    const SECONDS_MINUTE = 60;

    /**
     * Number of seconds in an hour
     */
    const SECONDS_HOUR = 3600;

    /**
     * Number of seconds in a day
     */
    const SECONDS_DAY = 86400;

    /**
     * Number of seconds in a week
     */
    const SECONDS_WEEK = 604800;

    /**
     * Number of seconds in a month
     */
    const SECONDS_MONTH = 2630000;

    /**
     * Number of seconds in a year
     */
    const SECONDS_YEAR = 31560000;
    
    /**
     * Number of bytes in a kilobyte
     */
    const BYTES_KILOBYTE = 1024;

    /**
     * Number of bytes in a megabyte
     */
    const BYTES_MEGABYTE = 1048576;

    /**
     * Number of bytes in a gigabyte
     */
    const BYTES_GIGABYTE = 1073741824;

    /**
     * Number of bytes in a terabyte
     */
    const BYTES_TERABYTE = 1099511627776;

    /**
     * Number of bytes in a petabyte
     */
    const BYTES_PETABYTE = 1125899906842624;

    /**
     * Byte size units
     * @var array
     */
    protected static $byte_units = ["B", "KB", "MB", "GB", "TB", "PB"];
    
    /**
     * Converts a byte number into a human readable format
     *
     * Examples:
     * ```php
     * echo Conversions::bytesToHuman(100);
     * // Outputs: "100B"
     * 
     * echo Conversions::bytesToHuman(1024);
     * // Outputs: "1KB"
     * 
     * echo Conversions::bytesToHuman(1050);
     * // Outputs: "1.02KB"
     * ```
     * 
     * @param  int $bytes The number of bytes
     * @param  int $decimals Number of decimal places to round to
     *                    
     * @return string
     */
    public static function bytesToHuman($bytes, $decimals = 2)
    {
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(self::BYTES_KILOBYTE));
        $pow = min($pow, count(self::$byte_units) - 1);
        $bytes /= pow(self::BYTES_KILOBYTE, $pow);
        
        return self::numberFormat($bytes, $decimals) . self::$byte_units[$pow];
    }

    /**
     * Format a number
     * 
     * Works exactly like the number_format() function. However, this method does not add decimal
     * places to whole numbers.
     * 
     * Examples:
     * ```php
     * echo Conversions::numberFormat(1024);
     * // Output: "1,024"
     * 
     * echo Conversions::numberFormat(1024.23);
     * // Outputs: "1,024.23"
     * ```
     * 
     * @param  float  $num              The number to format
     * @param  int    $decimals         The number of decimal places
     * @param  string $dec_point        The decimal point character
     * @param  string $thousands_sep    The thousands separator character
     *
     * @return string
     */
    public static function numberFormat($num, $decimals = 2, $dec_point = ".", $thousands_sep = ",")
    {
        $num = round($num, $decimals);
        if (floor($num) !== $num) {
            $parts    = explode(".", $num, 2);
            $parts[0] = number_format($parts[0], 0, $dec_point, $thousands_sep);
            $num      = "{$parts[0]}.{$parts[1]}";
        } else {
            $num = number_format($num, 0, $dec_point, $thousands_sep);
        }
        
        return $num;
    }
} 