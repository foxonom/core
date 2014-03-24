Headzoo Utilities
=================
A collection of use PHP utility classes and functions.

Requirements
------------
* PHP 5.4 or greater.

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

##### string Headzoo\Utilities\Strings::transformCamelCaseToUnderscore(string $str)
Transforming CamelCaseText to under_score_text.

```php
$str = Strings::transformCamelCaseToUnderscore("CamelCaseString");
var_dump($str);

// Output:
// string(17) "camel_case_string"
```

##### string Headzoo\Utilities\Strings::transformUnderscoreToCamelCase(string $str)
Transforming under_score_text to CamelCaseText.

```php
$str = Strings::transformUnderscoreToCamelCase("camel_case_string");
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

##### array Headzoo\Utilities\Arrays::join(array $arr, string $separator, callable $callback = null)
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

Change Log
----------
##### v0.2 - 2014/03/24
* Added the `Headzoo\Utilities\Complete` class.

##### v0.1 - 2014/03/23
* First version released under MIT license.

License
-------
This content is released under the MIT License. See the included LICENSE for more information.
