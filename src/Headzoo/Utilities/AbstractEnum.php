<?php
namespace Headzoo\Utilities;

/**
 * Abstract class for creating enumerator classes.
 * 
 * Unlike the primary purpose of enumerators from other languages, which is having the verbosity of strings with
 * the memory savings of integers, the enums created with this class have the purpose of reducing function
 * argument validation. For example, how many times have you written code like this:
 * 
 * ```php
 * function setWeekDay($week_day)
 * {
 *      $valid = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
 *      if (!in_array($week_day, $valid)) {
 *          throw new InvalidArgumentException("Invalid week day given.");
 *      }
 * 
 *      echo "You set the week day to {$week_day}.";
 * }
 * 
 * $week_day = "Tuesday";
 * setWeekDay($week_day);
 * ```
 * 
 * Instead of validating string arguments we can use enums and PHP's type hinting.
 * 
 * ```php
 * function setWeekDay(WeekDays $week_day)
 * {
 *      echo "You set the week day to {$week_day}.";
 * }
 * 
 * $week_day = new WeekDay("Tuesday");
 * setWeekDay($week_day);
 * ```
 * 
 * 
 * 
 * Example:
 * ```php
 * class DaysEnum
 *      extends AbstractEnum
 * {
 *      const SUNDAY    = "SUNDAY";
 *      const MONDAY    = "MONDAY";
 *      const TUESDAY   = "TUESDAY";
 *      const WEDNESDAY = "WEDNESDAY";
 *      const THURSDAY  = "THURSDAY";
 *      const FRIDAY    = "FRIDAY";
 *      const SATURDAY  = "SATURDAY";
 *      const __DEFAULT = self::SUNDAY;
 * }
 * 
 * $day = new DaysEnum("SUNDAY");
 * echo $day;
 * echo $day->value();
 * 
 * // Outputs:
 * // "SUNDAY"
 * // "SUNDAY"
 * 
 * 
 * // The default value is used when not specified.
 * $day = new DaysEnum();
 * echo $day;
 * 
 * // Outputs: "SUNDAY"
 * 
 * 
 * // The constructor value is not case-sensitive.
 * $day = new DaysEnum("sUndAy");
 * echo $day;
 * 
 * // Outputs: "SUNDAY"
 * 
 * // Enum values are easy to compare.
 * $day_tue1 = new DaysEnum(DaysEnum::TUESDAY);
 * $day_fri1 = new DaysEnum(DaysEnum::FRIDAY);
 * $day_tue2 = new DaysEnum(DaysEnum::TUESDAY);
 * $day_fri2 = new DaysEnum($day_fri1);
 * 
 * var_dump($day_tue1 == DaysEnum::TUESDAY);
 * var_dump($day_tue1 == $day_tue2);
 * var_dump($day_fri1 == $day_fri2);
 * var_dump($day_tue1 == DaysEnum::FRIDAY);
 * var_dump($day_tue1 == $day_fri1);
 * 
 * // Outputs:
 * // bool(true)
 * // bool(true)
 * // bool(true)
 * // bool(false)
 * // bool(false)
 * 
 * $day = DaysEnum(DaysEnum::TUESDAY);
 * switch($day) {
 *      case DaysEnum::SUNDAY:
 *          echo "Sunday!";
 *          break;
 *      case DaysEnum::MONDAY:
 *          echo "Monday!";
 *          break;
 *      case DaysEnum::TUESDAY:
 *          echo "Tuesday!";
 *          break;
 * }
 * 
 * // Outputs: "Tuesday!"
 * 
 * 
 * // Creating new instances with the factory method:
 * $day = DaysEnum::FRIDAY();
 * var_dump($day == DaysEnum::FRIDAY);
 * 
 * // Outputs: bool(true)
 * 
 * 
 * // Even instances objects have factories:
 * $day1 = new DaysEnum(DaysEnum::FRIDAY);
 * $day2 = $day1();
 * var_dump($day1 == $day2);
 * var_dump($day1 === $day2);
 * 
 * // Outputs:
 * // bool(true)
 * // bool(false)
 * ```
 * 
 * There are a few caveats for child classes:
 *  - They must defined a __DEFAULT constant.
 *  - The constant name and value must be the same (except for __DEFAULT).
 * 
 * Constructing a new instance when those two requirements are not met leads to
 * an exception being thrown.
 * 
 * Examples:
 * ```php
 * // Error, this enum does not define a __DEFAULT constant.
 * class PetsEnum
 *      extends AbstractEnum
 * {
 *      const DOG  = "DOG";
 *      const CAT  = "CAT";
 *      const BIRD = "BIRD";
 *      const FISH = "FISH";
 * }
 * 
 * // Error,the constant FRENCH_FRIES has the value "FRIES". They must match exactly.
 * class FoodsEnum
 *      extends AbstractEnum
 * {
 *      const PIZZA        = "PIZZA";
 *      const HAMBURGER    = "HAMBURGER";
 *      const HOT_DOG      = "HOT_DOG";
 *      const FRENCH_FRIES = "FRIES";
 *      const __DEFAULT    = self::PIZZA;
 * }
 * ```
 */
abstract class AbstractEnum
{
    use ConstantsTrait;

    /**
     * The constant value
     * @var string
     */
    public $value;
    
    

    /**
     * Constructor
     * 
     * @param string|AbstractEnum $value The enumerator value
     * @throws Exceptions\UndefinedConstantException When the child class does not define a __DEFAULT constant
     * @throws Exceptions\UndefinedConstantException When the child class does not have a constant with the given $value
     * @throws Exceptions\LogicException When the child class defines constants with non-string values
     */
    public function __construct($value = null)
    {
        $constants = $this->constants();
        if (!isset($constants["__DEFAULT"])) {
            throw new Exceptions\UndefinedConstantException(
                sprintf(
                    "Class %s must defined a __DEFAULT constant.",
                    get_called_class()
                )
            );
        }

        $value = strtoupper((string)$value);
        if (!$value) {
            $value = $constants["__DEFAULT"];
        }
        if (!isset($constants[$value])) {
            throw new Exceptions\UndefinedConstantException(
                sprintf(
                    "Class %s does not have a constant named %s.",
                    get_called_class(),
                    $value
                )
            );
        }
        
        foreach($constants as $n => $v) {
            if ("__DEFAULT" !== $n && $n !== $v) {
                throw new Exceptions\LogicException(
                    sprintf(
                        "Constant %s:%s does not match value %s.",
                        get_called_class(),
                        $n,
                        $v
                    )
                );
            }
        }
        
        $this->value = $value;
    }

    /**
     * Returns the value of the enum
     * 
     * @return string
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Returns whether the value is equal to this value
     *
     * Performs a strict comparison of this enum value, and another value. The other value may be either a string,
     * or another instance of the same enum.
     * 
     * @param  string|AbstractEnum $value The value to compare
     * @return bool
     */
    public function equals($value)
    {
        if (is_object($value) && get_class($value) == get_class($this)) {
            $equals = $value->value() === $this->value();
        } else {
            $equals = $this->value() === ((string)$value);
        }
        
        return $equals;
    }

    /**
     * Returns a new instance of this enum
     * 
     * When called without a value, the returned instance has the same value as this instance.
     * 
     * Example:
     * ```php
     * // The two enums will have the same value, and equal each other, but they are two different
     * // objects.
     * $day1 = new WeekDays();
     * $day2 = $day1();
     * 
     * var_dump($day1->equals($day2));
     * // Outputs: bool(true)
     * 
     * var_dump($day1 === $day2);
     * // Outputs: bool(false)
     * ```
     * 
     * When called with a value, the returned instance will have the value of the given value.
     *
     * Example:
     * ```php
     * // The two enums will have the same value, and equal each other, but they are two different
     * // objects.
     * $day1 = new WeekDays(WeekDays::MONDAY);
     * $day2 = $day1(WeekDays::FRIDAY);
     *
     * var_dump($day1->equals($day2));
     * // Outputs: bool(false)
     * ```
     * 
     * @param  string|AbstractEnum $value Value of the returned enum
     * @return AbstractEnum
     */
    public function __invoke($value = null)
    {
        $enum  = get_called_class();
        return new $enum($value ?: $this->value);
    }
    
    /**
     * Enum factory method
     * 
     * Allows for the creation of a new instance by calling a static method with the name
     * of a class constant.
     * 
     * Examples:
     * ```php
     * // This is the same...
     * $day = WeekDays::MONDAY();
     * 
     * // as this.
     * $day = new WeekDays(WeekDays::MONDAY);
     * ```
     * 
     * @param  string $value The value of the new enum
     * @param  array  $args Ignored
     * @return AbstractEnum
     */
    public static function __callStatic($value, $args)
    {
        $enum = get_called_class();
        return new $enum($value);
    }
    
    /**
     * Returns the value of the enum
     * 
     * This allows non-strict comparison of enums.
     * 
     * Example:
     * ```php
     * $day = new WeekDays(WeekDay::MONDAY);
     * switch($day) {
     *  case WeekDays::SUNDAY:
     *      echo "Found Sunday!";
     *      break;
     * case WeekDays::MONDAY:
     *      echo "Found Monday!";
     *      break;
     * case WeekDays::TUESDAY:
     *      echo "Found Tuesday!";
     *      break;
     * }
     * 
     * // Outputs: "Found Monday!"
     * ```
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->value();
    }
} 