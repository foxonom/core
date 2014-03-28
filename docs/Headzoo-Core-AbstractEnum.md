Headzoo\Core\AbstractEnum
===============

Abstract class for creating enumerator classes.

Unlike the primary purpose of enumerators from other languages, which is having the verbosity of strings with
the memory savings of integers, the enums created with this class have the purpose of reducing function
argument validation. For example, how many times have you written code like this:

```php
function setWeekDay($week_day)
{
     $valid = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
     if (!in_array($week_day, $valid)) {
         throw new InvalidArgumentException("Invalid week day given.");
     }

     echo "You set the week day to {$week_day}.";
}

$week_day = "Tuesday";
setWeekDay($week_day);
```

Instead of validating string arguments we can use enums and PHP's type hinting.

```php
function setWeekDay(WeekDays $week_day)
{
     echo "You set the week day to {$week_day}.";
}

$week_day = new WeekDay("Tuesday");
setWeekDay($week_day);
```



Example:
```php
class DaysEnum
     extends AbstractEnum
{
     const SUNDAY    = "SUNDAY";
     const MONDAY    = "MONDAY";
     const TUESDAY   = "TUESDAY";
     const WEDNESDAY = "WEDNESDAY";
     const THURSDAY  = "THURSDAY";
     const FRIDAY    = "FRIDAY";
     const SATURDAY  = "SATURDAY";
     const __DEFAULT = self::SUNDAY;
}

$day = new DaysEnum("SUNDAY");
echo $day;
echo $day->value();

// Outputs:
// "SUNDAY"
// "SUNDAY"


// The default value is used when not specified.
$day = new DaysEnum();
echo $day;

// Outputs: "SUNDAY"


// The constructor value is not case-sensitive.
$day = new DaysEnum("sUndAy");
echo $day;

// Outputs: "SUNDAY"

// Enum values are easy to compare.
$day_tue1 = new DaysEnum(DaysEnum::TUESDAY);
$day_fri1 = new DaysEnum(DaysEnum::FRIDAY);
$day_tue2 = new DaysEnum(DaysEnum::TUESDAY);
$day_fri2 = new DaysEnum($day_fri1);

var_dump($day_tue1 == DaysEnum::TUESDAY);
var_dump($day_tue1 == $day_tue2);
var_dump($day_fri1 == $day_fri2);
var_dump($day_tue1 == DaysEnum::FRIDAY);
var_dump($day_tue1 == $day_fri1);

// Outputs:
// bool(true)
// bool(true)
// bool(true)
// bool(false)
// bool(false)

$day = DaysEnum(DaysEnum::TUESDAY);
switch($day) {
     case DaysEnum::SUNDAY:
         echo "Sunday!";
         break;
     case DaysEnum::MONDAY:
         echo "Monday!";
         break;
     case DaysEnum::TUESDAY:
         echo "Tuesday!";
         break;
}

// Outputs: "Tuesday!"


// Creating new instances with the factory method:
$day = DaysEnum::FRIDAY();
var_dump($day == DaysEnum::FRIDAY);

// Outputs: bool(true)


// Even instances objects have factories:
$day1 = new DaysEnum(DaysEnum::FRIDAY);
$day2 = $day1();
var_dump($day1 == $day2);
var_dump($day1 === $day2);

// Outputs:
// bool(true)
// bool(false)
```

There are a few caveats for child classes:
 - They must defined a __DEFAULT constant.
 - The constant name and value must be the same (except for __DEFAULT).

Constructing a new instance when those two requirements are not met leads to
an exception being thrown.

Examples:
```php
// Error, this enum does not define a __DEFAULT constant.
class PetsEnum
     extends AbstractEnum
{
     const DOG  = "DOG";
     const CAT  = "CAT";
     const BIRD = "BIRD";
     const FISH = "FISH";
}

// Error,the constant FRENCH_FRIES has the value "FRIES". They must match exactly.
class FoodsEnum
     extends AbstractEnum
{
     const PIZZA        = "PIZZA";
     const HAMBURGER    = "HAMBURGER";
     const HOT_DOG      = "HOT_DOG";
     const FRENCH_FRIES = "FRIES";
     const __DEFAULT    = self::PIZZA;
}
```


* Class name: AbstractEnum
* Namespace: Headzoo\Core
* This is an **abstract** class
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)





Properties
----------


### $consts
Initialized enum constants


```php
private array $consts = array()
```

* This property is **static**.


### $value
The constant value


```php
private string $value
```



Methods
-------


### Headzoo\Core\AbstractEnum::__construct
Constructor

The new instance will be set to the value of $value, which may be a string, another instance
of the same class, or null. The default value will be used when not specified. When initialized
with an object, the object must be an instance of this class, or else an exception is thrown.
```php
public mixed Headzoo\Core\AbstractEnum::__construct(string|\Headzoo\Core\AbstractEnum $value)
```


##### Arguments

* $value **string|[Headzoo\Core\AbstractEnum](Headzoo-Core-AbstractEnum.md)** - The value of this enumerator



### Headzoo\Core\AbstractEnum::value
Returns the value of the enum


```php
public string Headzoo\Core\AbstractEnum::value()
```




### Headzoo\Core\AbstractEnum::equals
Returns whether the value is equal to this value

Performs a strict comparison of this enum value, and another value. The other value may be either a string,
or another instance of the same enum.
```php
public bool Headzoo\Core\AbstractEnum::equals(string|\Headzoo\Core\AbstractEnum $value)
```


##### Arguments

* $value **string|[Headzoo\Core\AbstractEnum](Headzoo-Core-AbstractEnum.md)** - The value to compare



### Headzoo\Core\AbstractEnum::__invoke
Returns a new instance of this enum

When called without a value, the returned instance has the same value as this instance.

Example:
```php
// The two enums will have the same value, and equal each other, but they are two different
// objects.
$day1 = new WeekDays();
$day2 = $day1();

var_dump($day1->equals($day2));
// Outputs: bool(true)

var_dump($day1 === $day2);
// Outputs: bool(false)
```

When called with a value, the returned instance will have the value of the given value.

Example:
```php
// The two enums will have the same value, and equal each other, but they are two different
// objects.
$day1 = new WeekDays(WeekDays::MONDAY);
$day2 = $day1(WeekDays::FRIDAY);

var_dump($day1->equals($day2));
// Outputs: bool(false)
```
```php
public Headzoo\Core\AbstractEnum Headzoo\Core\AbstractEnum::__invoke(string|\Headzoo\Core\AbstractEnum $value)
```


##### Arguments

* $value **string|[Headzoo\Core\AbstractEnum](Headzoo-Core-AbstractEnum.md)** - Value of the returned enum



### Headzoo\Core\AbstractEnum::__callStatic
Enum factory method

Allows for the creation of a new instance by calling a static method with the name
of a class constant.

Examples:
```php
// This is the same...
$day = WeekDays::MONDAY();

// as this.
$day = new WeekDays(WeekDays::MONDAY);
```
```php
public Headzoo\Core\AbstractEnum Headzoo\Core\AbstractEnum::__callStatic(string $value, array $args)
```

* This method is **static**.

##### Arguments

* $value **string** - The value of the new enum
* $args **array** - Ignored



### Headzoo\Core\AbstractEnum::__toString
Returns the value of the enum

This allows non-strict comparison of enums.

Example:
```php
$day = new WeekDays(WeekDay::MONDAY);
switch($day) {
 case WeekDays::SUNDAY:
     echo "Found Sunday!";
     break;
case WeekDays::MONDAY:
     echo "Found Monday!";
     break;
case WeekDays::TUESDAY:
     echo "Found Tuesday!";
     break;
}

// Outputs: "Found Monday!"
```
```php
public string Headzoo\Core\AbstractEnum::__toString()
```




### Headzoo\Core\AbstractEnum::validate
Validates the enum class definition for correctness

Checks the class has defined a __DEFAULT constant, and that the constant names and values
match each other. This check only needs to be performed the first time an instance of the
class is created, so the results of the validation are cached in the self::$consts property.
Future invocations of the class will skip the validation checks.

The self::$consts property must be static so each instance has access to the same cache data.
Once a class has been validated, an array of the class constants are saved in the self::$consts
array using the name of the validated class as the key.

Returns an array of the class constants.
```php
private array Headzoo\Core\AbstractEnum::validate()
```

* This method is **static**.



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\AbstractEnum::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\AbstractEnum::getNamespaceName()
```




### Headzoo\Core\Obj::toss
Throws an exception from the calling class namespace

Examples:
```php
// If the calling class namespace is Headzoo\Core this call will throw an
// instance of Headzoo\Core\Exceptions\InvalidArgumentException with the
// given message.
$this->toss("InvalidArgumentException", "There was an error.");

// Additional context arguments may be passed to the method which will be interpolated
// into the message. The interpolater looks for numerically indexed place holders,
// eg {0}, {1}, etc, which map to the extra arguments. This means the context arguments
// may be given in any order.
$this->toss("RuntimeException", "The {0} system broke.", "database");

// The context interpolater has a few built-in place holders. The "{me}" place holder
// will be replaced with the name of the class which threw the exception. Additional
// context arguments are inserted into the message per their index.
$this->toss("RuntimeException", "The {me} class reported a {0} error.", "serious");

// When the first argument after the message is an integer, it will be used as the
// exception code. This call will throw an instance of
// Headzoo\Core\Exceptions\RuntimeException with the message "There was an error",
// and the error code 43.
$this->toss("RuntimeException", "There was an error.", 43);

// This call is giving an exception code, and context arguments for interpolation.
// Remember when the first argument after the message is an integer, it's treated as
// the error code. When you need a number to be interpolated into the message, cast
// it to a string.
$this->toss("RuntimeException", "There was a {0} error", 43, "database");

// For exceptions in the Headzoo\Core namespace, the word "Exception" in the name
// of the exception is optional.
$this->toss("InvalidArgument", "There was an error.");
$this->toss("Runtime", "The {0} system broke.", "database");
```

The built in place holders:
 {me}        - The name of the class throwing the exception
 {exception} - The name of the exception being thrown
 {code}      - The exception code
 {date}      - The date the exception was thrown
```php
protected mixed Headzoo\Core\AbstractEnum::toss(string $exception, string $message, int $code)
```

* This method is **static**.

##### Arguments

* $exception **string** - The name of the exception to throw
* $message **string** - The error message
* $code **int** - The error code, defaults to 0



### Headzoo\Core\Obj::interpolate
Interpolates context values into the message placeholders.

Taken from PSR-3's example implementation.
```php
private string Headzoo\Core\AbstractEnum::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


