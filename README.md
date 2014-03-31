Headzoo Core v0.5
=================

A collection of use PHP utility classes and functions.

- [Overview](#overview)
- [Requirements](#requirements)
- [Installing](#installing)
- [Class Overview](#class-overview)
- [Quick Start](#quick-start)
- [Change Log](#change-log)
- [License](#license)


Overview
--------
This project is a collection of classes, methods, and functions which I've created over the years, and I use  them in most of my other projects. The purpose of this project is putting all my useful code in one place. I'll be adding to this framework as I find useful classes from my other projects that I think should go into the core.


Requirements
------------
* [PHP 5.5 or greater](https://php.net/downloads.php).
* [psr/Log](https://github.com/php-fig/log).


Installing
----------
The library may be installed using either git or composer.

##### Git
Simply clone the project with the following command.

```
git clone git@github.com:headzoo/core.git
```

##### Composer
Add the project to your composer.json as a dependency.

```
"require": {
    "headzoo/core" : "dev-master"
}
```

Class Overview
--------------
The full class API documentation is available in the [/docs](docs/README.md) directory and the [wiki](https://github.com/headzoo/core/wiki).

##### [Core\Arrays](https://github.com/headzoo/core/wiki/Arrays)  
Contains static methods for working with arrays.

##### [Core\Strings](https://github.com/headzoo/core/wiki/Strings)  
Contains static methods for working with strings.

##### [Core\Objects](https://github.com/headzoo/core/wiki/Objects)  
Contains static methods for working with objects and classes.

##### [Core\ErrorHandler](https://github.com/headzoo/core/wiki/ErrorHandler)  
Used to capture and gracefully handle core errors and exceptions.

##### [Core\Errors](https://github.com/headzoo/core/wiki/Errors)  
Utility class used to work with E_ERROR constants.

##### [Core\Profiler](https://github.com/headzoo/core/wiki/Profiler)  
Used to profile code.

##### [Core\Conversions](https://github.com/headzoo/core/wiki/Conversions)  
Utility class for converting from one value to another.

##### [Core\ConstantsTrait](https://github.com/headzoo/core/wiki/ConstantsTrait)  
Trait for reflecting on class constants.

##### [Core\AbstractEnum](https://github.com/headzoo/core/wiki/AbstractEnum)  
Abstract class for creating enumerator classes.

##### [Core\Complete](https://github.com/headzoo/core/wiki/Complete)  
Used to call a function when the object destructs.

##### [Core\Functions](https://github.com/headzoo/core/wiki/Functions)  
Contains static methods for working with functions and methods.


Quick Start
-----------
This quick start guide *briefly* goes over a few of the classes. The full class API documentation is available in the [/docs](docs/README.md) directory.


#### Core\Strings
```php
echo Strings::camelCaseToUnderscore("CamelCaseString");
// Outputs: "camel_case_string"

echo Strings::camelCaseToUnderscore("MaryHadALittleLamb");
// Outputs: "mary_had_a_little_lamb"


$is = Strings::startsUpper("Welcome my son, welcome to the machine.");
var_dump($is);

// Output: bool(true);

$is = Strings::startsUpper("you've been in the pipeline, filling in time");
var_dump($is);

// Output: bool(false)


// Truncating a string at the end.
echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_END);

// Outputs: "Mary had a little..."

// Truncating a string at the start.
echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_START);

// Outputs: "...as white as snow."

// Truncating a string in the middle.
echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_MIDDLE);

// Outputs: "Mary ha...e as snow."
```

#### Core\Arrays
```php
$array = [
   "headzoo",
   "joe",
   "sam"
];

echo Arrays::conjunct($array);
// Outputs: headzoo, joe, and sam

// Using a callback to quote the array values.
echo Arrays::conjunct($array, "and", 'Headzoo\Core\Strings::quote');
// Outputs: 'headzoo', 'joe', and 'sam'


$arr = [
 0 => [
     "username" => "headzoo",
     "email" => "sean@headzoo.io"
 ],
 1 => [
     "username" => "joe",
     "email" => "joe@headzoo.io"
 ]
]

$ret = Arrays::column($arr, "username");

// Outputs: ["headzoo", "joe"]
```

#### Core\Objects
```php
// Testing whether an object is an instance of another.
$is = Objects::isInstance(new stdClass(), stdClass);
var_dump($is);
// Outputs: bool(true)

// Unlike the instanceof operator, the second argument can be a string.
$is = Objects::isInstance(new stdClass(), 'stdClass');
var_dump($is);
// Outputs: bool(true);

// You can even test an array of objects.
$objects = [
    new stdClass(),
    new stdClass()
];
$is = Objects::isInstance($objects, stdClass);
var_dump($is);
// Outputs: bool(true)
```

#### Core\ErrorHandler
```php
// Capture all errors, and display an error page instead of the usual php
// error message.
$handler = new ErrorHandler();
$handler->handle();

// Setup your own way of handing errors.
$handler = new ErrorHandler();
$handler->setCallback(function($handler) {
	include("template/error.php");
});
$handler->handle()

// Even handle errors different in different environments.
$handler = new ErrorHandler();
$handler->setCallback("dev", function($handler) {
	include("template/error_dev.php");
});
$handler->setCallback("live", function($handler) {
	include("template/error_live.php");
});
$handler->handle("live");
```

#### Core\Profiler
```php
// The most basic profiling.
$profiler = new Profiler();
$profiler->start();
... do something here ...
$micro = $profiler->stop();
var_dump($micro);

// Outputs:
// "Profile time for 'default': 0.00030207633972168"
// double(0.00030207633972168)


// This example runs the closure 100 times, and displays the profile results.
Profiler::run(100, true, function() {
  ... do something here ...
});

// Output:
//
// Total Runs:                 100
// Total Time:      0.099596977234
// Average Time:    0.000981624126
// -------------------------------
// Run #1           0.000479936599
// Run #2           0.000968933105
// Run #3           0.000982999801
// Run #4           0.000988006591
// ......
// Run #97          0.000985145568
// Run #98          0.000983953476
// Run #99          0.000997066497
// Run #100         0.000993013382
```

#### Core\AbstractEnum
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
```

#### Core\Functions
```php
// The Functions::swapCallable() method is used to swap two arguments when the second is a callable. It's used
// to create functions/methods which have callbacks as the final argument, and it's desirable to make middle
// argument optional, while the callback remains the final argument.

function joinArray(array $values, $separator, callable $callback = null)
{
   Functions::swapCallable($separator, $callback, "-");
   $values = array_map($callback, $values);
   return join($separator, $values);
}

// The function above may be called normally, like this:
$values = ["headzoo", "joe"];
joinArray($values, "-", 'Headzoo\Core\String::quote');

// Or the middle argument may be omitted, and called like this:
joinArray($values, 'Headzoo\Core\String::quote');
```

#### Core\Complete
```php
// In this example the database connection will always be closed, even if the $database->fetch()
// method throws an exception, because the anonymous function passed to Complete::factory()
// is called when the $complete object goes out of scope.

$database = new FakeDatabase();
$complete = Complete::factory(function() use($database) {
   $database->close();
});
try {
   $rows = $database->fetch();
} catch (Exception $e) {
   echo $e->getTraceAsString();
   throw $e;
}
```

Change Log
----------
##### v0.5.0 - 2014/03/31
* Created the `Conversions` class.
* The `Profiler::run` method outputs memory usage.
* Refactored the `ErrorHandler` class.

##### v0.4.1 - 2014/03/30
* Created the `Functions::swapArgs` method.
* Refactored some of the code in the `ErrorsHandler` class.

##### v0.4.0 - 2014/03/30
* Created the `ErrorsHandler` class.
* Created the `Errors` class.
* Created the `Arrays::remove` method.

##### v0.3.2 - 2014/03/29
* Created the `Strings::truncate` method.
* Removed the `Strings::split` method.
* Created the `Profiler` class.
* Made `psr/Log` a requirement.

##### v0.3.1 - 2014/03/27
* Merged the `Validator` class into the `Functions` class.

##### v0.3 - 2014/03/26
* Renamed the namespace `Headzoo\Utilities` to `Headzoo\Core`.
* Renamed the project `headzoo/core`.
* Created core class `Obj`.
* Added the trait `ConstantsTrait`.
* Created the `ConstantsTrait` trait.
* Created the `AbstractEnum` class.
* The `Strings` class is now made to work seamlessly with multi-byte strings.
* Renamed `Strings::transformCamelCaseToUnderscore` to `Strings::camelCaseToUnderscore`.
* Renamed `Strings::transformUnderscoreToCamelCase` to `Strings::underscoreToCamelCase`.
* Added new methods to the `Strings` class:
    * `Strings::startsWith`.
    * `Strings::endsWith`.
    * `Strings::startsUpper`.
    * `Strings::startsLower`.
    * `Strings::replace`.
    * `Strings::length`.
    * `Strings::chars`.
    * `Strings::toUpper`.
    * `Strings::toLower`.
    * `Strings::ucFirst`.
    * `Strings::lcFirst`.
    * `Strings::title`.
    * `Strings::sub`.
    * `Strings::split`.
    * `Strings::transform`.
    
##### v0.2.3 - 2014/03/25
* Increased the minimum PHP version requirement to 5.5.0. Long live, ClassName::class!
* Added the method `Strings::quote`.
* Added the method `Arrays::conjunct`.
* Added the method `Functions::swapCallable`.
* Added the class `Validator`.

##### v0.2.2 - 2014/03/24
* Added the method `Arrays::findString`.

##### v0.2.1 - 2014/03/24
* Changed visibility of `Complete::invoke` to public.

##### v0.2 - 2014/03/24
* Added the `Complete` class.

##### v0.1 - 2014/03/23
* First version released under MIT license.

TODO
----
* Replace `Strings` constants with enums.

License
-------
This content is released under the MIT License. See the included LICENSE for more information.
