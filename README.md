Headzoo Utilities v3.0
======================
A collection of use PHP utility classes and functions.

Requirements
------------
* PHP 5.5 or greater.

Installing
----------
The library may be installed using either git or composer.

##### Git
Simply clone the project with the following command.

```
git clone git@github.com:headzoo/utilities.git
```

##### Composer
Add the project to your composer.json as a dependency.

```
"require": {
    "headzoo/utilities" : "dev-master"
}
```

Class Documentation
-------------------

#### Headzoo\Utilities\Strings
Contains static methods for working with strings.

##### string Headzoo\Utilities\Strings::random(string $len, int $char_class = null)
Generating random strings

```php
$password = Strings::random(
    10, 
    Strings::CHARS_LOWER | Strings::CHARS_UPPER | Strings::CHARS_NUMBERS | Strings::CHARS_PUNCTUATION
);
var_dump($password);

// Example output:
// string(10) "x87t,N5N2+"
```

##### string Headzoo\Utilities\Strings::camelCaseToUnderscore(string $str)
Transforming CamelCaseText to under_score_text.

```php
$str = Strings::camelCaseToUnderscore("CamelCaseString");
var_dump($str);

// Output:
// string(17) "camel_case_string"
```

##### string Headzoo\Utilities\Strings::underscoreToCamelCase(string $str)
Transforming under_score_text to CamelCaseText.

```php
$str = Strings::underscoreToCamelCase("camel_case_string");
var_dump($str);

// Output:
// string(15) "CamelCaseString"
```

#### Headzoo\Utilities\Arrays
Contains static methods for working with arrays.

##### array Headzoo\Utilities\Arrays::containsKeyValue(array $arr, string $key, mixed $value, bool $multi = true)
Determine if a key/value pair exists in a multidimensional array.

```php
$arr = [
    "admins" => [
        "headzoo" => "sean@headzoo.io",
        "joe"     => "joe@headzoo.io"
    ],
    "mods" => [
        "sam"     => "sam@headzoo.io"
    ]
];
var_dump(Arrays::containsKeyValue($arr, "headzoo", "sean@headzoo.io"));

// Outputs:
bool(true)
```

##### array Headzoo\Utilities\Arrays::column(array $arr, string $column)
Returns an array of column values.

```php
$arr = [
    0 => [
        "username" => "headzoo",
        "email"    => "sean@headzoo.io"
    ],
    1 => [
        "username" => "joe",
        "email"    => "joe@headzoo.io"
    ]
];
$arr = Arrays::column($arr, "username");
print_r($arr);

// Outputs:
// Array
// (
//     [0] => headzoo
//     [1] => joe
// )
```

##### array Headzoo\Utilities\Arrays::columnFilter(array $arr, string $column, callable $callback)
Filter columns of an array using a callback function.

```php
$arr = [
    0 => [
        "username" => "headzoo",
        "email"    => "sean@headzoo.io",
        "admin"    => true
    ],
    1 => [
        "username" => "joe",
        "email"    => "joe@headzoo.io",
        "admin"    => false
    ],
    2 => [
        "username" => "sam",
        "email"    => "sam@headzoo.io",
        "admin"    => true
    ]
];
$arr = Arrays::columnFilter($arr, "username", function($element) { return $element["admin"]; });
print_r($arr);

// Outputs:
// Array
// (
//     [0] => headzoo
//     [1] => sam
// )
```

##### string Headzoo\Utilities\Arrays::join(array $arr, string $separator, callable $callback = null)
Joins the elements of an array using an optional callback.

```php
$arr = [
    "HEADZOO",
    "JOE",
    "SAM"
];
$arr = Arrays::join($arr, ", ", "strtolower");
print_r($arr);

// Outputs:
// Array
// (
//     [0] => headzoo
//     [1] => joe
//     [2] => sam
// )
```

##### string Headzoo\Utilities\Arrays::conjunct(array $arr, string $conjunction, callable $callback = null)
Similar to the Arrays::join() method, this method combines the array values using the default separator,
and joins the final item in the array with a conjunction. An array of strings can be turned into
a list of items, for example ["food", "water", "shelter"] becomes '"food, water, and shelter"'.

```php
$arr = [
    "headzoo",
    "joe",
    "sam"
];

echo Arrays::conjunct($arr);
// Outputs: "headzoo, joe, and sam"

echo Arrays::conjunct($arr, "or");
// Outputs: "headzoo, joe, or sam"

echo Arrays::conjunct($arr, "and", 'Headzoo\Utilities\String::quote');
// Outputs: "'headzoo', 'joe', and 'sam'"

echo Arrays::conjunct($arr, 'Headzoo\Utilities\String::quote');
// Outputs: "'headzoo', 'joe', and 'sam'"
```

##### mixed Headzoo\Utilities\Arrays::findString(array $arr, mixed $needle, bool $reverse = false)
Finds the first or last occurrence of a string within an array. Similar to the array_search() function, this method
only searches for strings, and does so in a case-insensitive manner.

```php
$arr = [
    "headzoo",
    "joe",
    "sam",
    "sam",
    "666",
    "headzoo"
];

echo Arrays::findString($arr, "JoE");
// Outputs: 1

echo Arrays::findString($arr, "HEADZOO");
// Outputs: 0

echo Arrays::findString($arr, "headzoo", true);
// Outputs: 5

echo Arrays::findString($arr, "amy");
// Outputs: false
```

#### Headzoo\Utilities\Complete
The class wraps a callable function, which is called in the class destructor. The utility of this scheme is the ability
to ensure the function is called eventually. Usually when the Complete object goes out of scope, which is when it's
destructor is called.

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

#### Headzoo\Utilities\Validator
Performs simple validation on values.

##### bool Headzoo\Utilities\Validator::validateRequired(array $values, array $required, bool $allowEmpty = false)
Throws an exception when required values are missing from an array of key/value pairs.

```php
$values = [
    "name"   => "headzoo",
    "job"    => "circus animal",
    "age"    => 38,
    "gender" => "male"
];
$required = [
    "name",
    "age",
    "gender"
];

// This is valid. All required values exists.
$this->validator->validateRequired($values, $required);

$values = [
    "name"   => "headzoo",
    "job"    => "circus animal"
];
$required = [
    "name",
    "age",
    "gender"
];

// This will throw an exception because the "age" value is missing.
$this->validator->validateRequired($values, $required);
```

#### Headzoo\Utilities\Functions
Contains static methods for working with functions and methods.

##### bool Headzoo\Utilities\Functions::swapCallable(mixed &$optional, mixed &$callable, mixed $default = null)
Swaps two variables when the second is a callable object. Used to create functions/methods which have a callback as
the final argument, and it's desirable to make middle argument optional, while the callback remains the final argument.

```php
function joinArray(array $values, $separator = "-", callable $callback = null)
{
   Functions::swapCallable($separator, $callback, "-");
   $values = array_map($callback, $values);
   return join($separator, $values);
}

// The function above may be called normally, like this:
$values = ["headzoo", "joe"];
joinArray($values, "-", 'Headzoo\Utilities\String::quote');

// Or the middle argument may be omitted, and called like this:
joinArray($values, 'Headzoo\Utilities\String::quote');
```

#### Headzoo\Utilities\ConstantsTrait
Trait for reflecting on class constants.

```php
class WeekDays
{
   use ConstantsTrait;

   const SUNDAY    = "Sunday";
   const MONDAY    = "Monday";
   const TUESDAY   = "Tuesday";
   const WEDNESDAY = "Wednesday";
   const THURSDAY  = "Thursday";
   const FRIDAY    = "Friday";
   const SATURDAY  = "Saturday";
}

// Returns the class constants an array of name/value pairs.
$constants = WeekDays::constants();
print_r($constants);

// Outputs:
[
   "SUNDAY"    => "Sunday",
   "MONDAY"    => "Monday",
   "TUESDAY"   => "Tuesday",
   "WEDNESDAY" => "Wednesday",
   "THURSDAY"  => "Thursday",
   "FRIDAY"    => "Friday",
   "SATURDAY"  => "Saturday"
]

// Returns the names of the class constants.
$names = WeekDays::constantNames();
print_r($names);

// Outputs:
[
   "SUNDAY",
   "MONDAY",
   "TUESDAY",
   "WEDNESDAY",
   "THURSDAY",
   "FRIDAY",
   "SATURDAY"
]

// Returns the values of the class constants.
$values = WeekDays::constantValues();
print_r($values);

// Outputs:
[
   "Sunday",
   "Monday",
   "Tuesday",
   "Wednesday",
   "Thursday",
   "Friday",
   "Saturday"
]

// Returns the value for the given constant name. This method throws an
// exception when a constant with the given name does not exit, which should
// be discovered during development.
echo WeekDays::constant("SUNDAY");
echo WeekDays::constant("tuesday");
echo WeekDays::constant("Friday");

// Outputs:
"Sunday"
"Tuesday"
"Friday"
```

Change Log
----------
##### v0.3 - 2014/03/26
* Added the trait `ConstantsTrait`.
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

License
-------
This content is released under the MIT License. See the included LICENSE for more information.
