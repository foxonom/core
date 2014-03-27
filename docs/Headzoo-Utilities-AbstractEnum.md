Headzoo\Utilities\AbstractEnum
===============

Abstract class for creating enumerator classes.

Child classes define constants which become the enumerator values. The instances of those child
classes can be easily compared and copied.

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
* Namespace: Headzoo\Utilities
* This is an **abstract** class





Properties
----------


### $value
The constant value


```php
public string $value
```



Methods
-------


### Headzoo\Utilities\AbstractEnum::__construct
Constructor


```php
public mixed Headzoo\Utilities\AbstractEnum::__construct(string|\Headzoo\Utilities\AbstractEnum $value)
```


##### Arguments

* $value **string|[Headzoo\Utilities\AbstractEnum](Headzoo-Utilities-AbstractEnum.md)** - The enumerator value



### Headzoo\Utilities\AbstractEnum::value
Returns the value of the enum


```php
public string Headzoo\Utilities\AbstractEnum::value()
```




### Headzoo\Utilities\AbstractEnum::equals
Returns whether the value is equal to this value

Performs a strict comparison of this enum value, and another value. The other value may be either a string,
or another instance of the same enum.
```php
public bool Headzoo\Utilities\AbstractEnum::equals(string|\Headzoo\Utilities\AbstractEnum $value)
```


##### Arguments

* $value **string|[Headzoo\Utilities\AbstractEnum](Headzoo-Utilities-AbstractEnum.md)** - The value to compare



### Headzoo\Utilities\AbstractEnum::__invoke
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
public Headzoo\Utilities\AbstractEnum Headzoo\Utilities\AbstractEnum::__invoke(string|\Headzoo\Utilities\AbstractEnum $value)
```


##### Arguments

* $value **string|[Headzoo\Utilities\AbstractEnum](Headzoo-Utilities-AbstractEnum.md)** - Value of the returned enum



### Headzoo\Utilities\AbstractEnum::__callStatic
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
public Headzoo\Utilities\AbstractEnum Headzoo\Utilities\AbstractEnum::__callStatic(string $value, array $args)
```

* This method is **static**.

##### Arguments

* $value **string** - The value of the new enum
* $args **array** - Ignored



### Headzoo\Utilities\AbstractEnum::__toString
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
public string Headzoo\Utilities\AbstractEnum::__toString()
```



