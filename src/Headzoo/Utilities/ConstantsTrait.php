<?php
namespace Headzoo\Utilities;
use ReflectionClass;

/**
 * Trait for reflecting on class constants.
 * 
 * Example:
 * ```php
 * class WeekDays
 * {
 *      use ConstantsTrait;
 *
 *      const SUNDAY    = "Sunday";
 *      const MONDAY    = "Monday";
 *      const TUESDAY   = "Tuesday";
 *      const WEDNESDAY = "Wednesday";
 *      const THURSDAY  = "Thursday";
 *      const FRIDAY    = "Friday";
 *      const SATURDAY  = "Saturday";
 * }
 * 
 * $constants = WeekDays::constants();
 * print_r($constants);
 * 
 * // Outputs:
 * [
 *      "SUNDAY"    => "Sunday",
 *      "MONDAY"    => "Monday",
 *      "TUESDAY"   => "Tuesday",
 *      "WEDNESDAY" => "Wednesday",
 *      "THURSDAY"  => "Thursday",
 *      "FRIDAY"    => "Friday",
 *      "SATURDAY"  => "Saturday"
 * ]
 * 
 * $names = WeekDays::constantNames();
 * print_r($names);
 * 
 * // Outputs:
 * [
 *      "SUNDAY",
 *      "MONDAY",
 *      "TUESDAY",
 *      "WEDNESDAY",
 *      "THURSDAY",
 *      "FRIDAY",
 *      "SATURDAY"
 * ]
 * 
 * $values = WeekDays::constantValues();
 * print_r($values);
 * 
 * // Outputs:
 * [
 *      "Sunday",
 *      "Monday",
 *      "Tuesday",
 *      "Wednesday",
 *      "Thursday",
 *      "Friday",
 *      "Saturday"
 * ]
 * 
 * echo WeekDays::constant("SUNDAY");
 * echo WeekDays::constant("tuesday");
 * echo WeekDays::constant("Friday");
 * 
 * // Outputs:
 * "Sunday"
 * "Tuesday"
 * "Friday"
 * ```
 */
trait ConstantsTrait
{
    /**
     * Constants defined in classes using the trait
     * 
     * Must be static because the methods using it are static. Because every
     * class using the trait share the same static values, this array is
     * keyed by class names, with the value being the class constants.
     * 
     * @var array
     */
    private static $__constants = [];
    
    /**
     * Returns an array of the class constants
     * 
     * The returned array are key/value pairs, with the name of the constants being the
     * keys, and the constant values being the values.
     * 
     * Returns an empty array when the class does not have any constants.
     * 
     * @return array
     */
    public static function constants()
    {
        $class = get_called_class();
        if (!isset(self::$__constants[$class])) {
            $reflection = new ReflectionClass($class);
            self::$__constants[$class] = $reflection->getConstants();
        }
        
        return self::$__constants[$class];
    }

    /**
     * Returns the value of a specific class constant
     *
     * Looks for a class constant with the given name in a case-insensitive manner, and returns
     * the value of the constant if found.
     * 
     * Throws an Exceptions\UndefinedConstantException if a constant with the given name is not
     * defined in the class.
     * 
     * @param  string $name The name of a class constant
     * @return string
     * @throws Exceptions\UndefinedConstantException When the constant does not exit
     */
    public static function constant($name)
    {
        $name      = strtolower($name);
        $value     = null;
        $constants = array_change_key_case(self::constants(), CASE_LOWER);
        if (isset($constants[$name])) {
            $value = $constants[$name];
        } else {
            throw new Exceptions\UndefinedConstantException(
                "Undefined constant {$name}."
            );
        }
        
        return $value;
    }

    /**
     * Returns an array of the class constant names
     * 
     * Returns an empty array if the class does not have any constants.
     * 
     * @return array
     */
    public static function constantNames()
    {
        return array_keys(self::constants());
    }

    /**
     * Returns an array of the class constant values
     * 
     * Returns an empty array if the class does not have any constants.
     * 
     * @return array
     */
    public static function constantValues()
    {
        return array_values(self::constants());
    }
} 