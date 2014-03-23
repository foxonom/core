<?php
namespace Headzoo\Utilities;

/**
 * Contains static methods for working with arrays.
 */
class Arrays
{
    /**
     * Returns true if the $array contains the key $key with the value $value
     *
     * Searches array $array for the key $key, and returns true if the key is found,
     * and the value of the key is $value. Returns false if the key does not exist, or
     * the key value does not equal $value.
     *
     * @param  array  $array The array to scan
     * @param  string $key   The key to find
     * @param  mixed  $value The key value
     * @param  bool   $multi Is $array a multidimensional array?
     * @return bool
     */
    public static function containsKeyValue(array $array, $key, $value, $multi = true)
    {
        if (!$multi) return (array_key_exists($key, $array) && $array[$key] == $value);
        foreach($array as $sub) {
            foreach($sub as $k => $v) {
                if ($k == $key && $v == $value) return true;
            }
        }
        return false;
    }

    /**
     * Returns an array of column values from $array
     *
     * Returns an array of values from a multidimensional array where the
     * key equals $column. Similar to the way databases can return a list of
     * column values from a list of matched rows.
     *
     * Example:
     * ```php
     *  $arr = [
     *      0 => [
     *          "username" => "headzoo",
     *          "email" => "sean@headzoo.io"
     *      ],
     *      1 => [
     *          "username" => "joe",
     *          "email" => "joe@headzoo.io"
     *      ]
     *  ]
     *  
     *  $ret = Arrays::column($arr, "username");
     * ```
     * 
     * Returns:
     *  `["headzoo", "joe"]`
     * 
     * @param  array  $array  The array with values
     * @param  string $column The column name
     * @return array
     */
    public static function column(array $array, $column)
    {
        $columns = array();
        foreach($array as $value) {
            if (isset($value[$column])) {
                $columns[] = $value[$column];
            }
        }
        return $columns;
    }

    /**
     * Filters columns of an array using a callback function
     *
     * Similar to the column() method, this method returns an array of values
     * from a multidimensional array where the key equals $column and the list
     * of column values filtered by a callback function. Each element in $array
     * is passed to the callback function and the callback returns true to keep
     * the element, or false to remove it.
     *
     * The callback function will receive each item in the array as the first
     * argument, and the array index/key as the second argument.
     *
     * Example:
     * ```php
     *  $a = [
     *      0 => [
     *          "username" => "headzoo",
     *          "email"    => "sean@headzoo.io",
     *          "admin"    => true
     *      ],
     *      1 => [
     *          "username" => "joe",
     *          "email"    => "joe@headzoo.io",
     *          "admin"    => false
     *      ],
     *      2 => [
     *          "username" => "sam",
     *          "email"    => "sam@headzoo.io",
     *          "admin"    => true
     *      ]
     *  ];
     *  $ret = Arrays::columnFilter($a, "username", function($element) { return $element["admin"]; });
     * ```
     * 
     * Returns:
     *  `["headzoo", "sam"]`
     *
     * @param  array    $array    Multidimensional array
     * @param  string   $column   Name of the column
     * @param  callable $callback Filtering function
     * @return array
     */
    public static function columnFilter(array $array, $column, $callback)
    {
        foreach($array as $key => $value) {
            if (!$callback($value, $key)) {
                unset($array[$key]);
            }
        }
        return self::column($array, $column);
    }

    /**
     * Joins the elements of an array using an optional callback
     *
     * Works exactly the same as php's join() method, however each element of
     * the array will be passed to the callback function. The callback return
     * value is what gets joined.
     *
     * Example:
     * ```php
     *  $arr = [
     *      "HEADZOO",
     *      "JOE",
     *      "SAME"
     *  ];
     *  $ret = Arrays::join($arr, ", ", "strtolower");
     * ```
     *
     * Returns:
     *  `"headzoo, joe, sam"`
     *
     * @param  array    $array     The array to join
     * @param  string   $separator The separator string
     * @param  callable $callback  Callback applied to each element of the array
     * @return string
     */
    public static function join(array $array, $separator, $callback = null)
    {
        if (null !== $callback) {
            $array = array_map($callback, $array);
        }
        return join($separator, $array);
    }
}