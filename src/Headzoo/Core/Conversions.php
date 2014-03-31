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
    const MINUTE = 60;

    /**
     * Number of seconds in an hour
     */
    const HOUR = 3600;

    /**
     * Number of seconds in a day
     */
    const DAY = 86400;

    /**
     * Number of seconds in a week
     */
    const WEEK = 604800;

    /**
     * Number of seconds in a month
     */
    const MONTH = 2630000;

    /**
     * Number of seconds in a year
     */
    const YEAR = 31560000;
    
    /**
     * Number of bytes in a kilobyte
     */
    const KILOBYTE = 1024;

    /**
     * Number of bytes in a megabyte
     */
    const MEGABYTE = 1048576;

    /**
     * Number of bytes in a gigabyte
     */
    const GIGABYTE = 1073741824;

    /**
     * Number of bytes in a terabyte
     */
    const TERABYTE = 1099511627776;

    /**
     * Number of bytes in a petabyte
     */
    const PETABYTE = 1125899906842624;

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
        $pow = floor(($bytes ? log($bytes) : 0) / log(self::KILOBYTE));
        $pow = min($pow, count(self::$byte_units) - 1);
        $bytes /= pow(self::KILOBYTE, $pow);
        
        return self::numberFormat($bytes, $decimals) . self::$byte_units[$pow];
    }

    /**
     * Format a number
     * 
     * Works exactly like the number_format() function. However, this method does not add decimal
     * places to whole numbers, and trailing 0 are removed.
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
     * @param  int    $dec_max          The maximum number of decimal places
     * @param  string $dec_point        The decimal point character
     * @param  string $thousands_sep    The thousands separator character
     *
     * @return string
     */
    public static function numberFormat($num, $dec_max = 2, $dec_point = ".", $thousands_sep = ",")
    {
        if (floor($num) != $num) {
            $num = trim(
                number_format($num, $dec_max, $dec_point, $thousands_sep), 
                "0"
            );
        } else {
            $num = number_format((double)$num, 0, $dec_point, $thousands_sep);
        }
        
        return $num;
    }
} 