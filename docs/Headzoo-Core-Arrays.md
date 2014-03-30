Headzoo\Core\Arrays
===============

Contains static methods for working with arrays.




* Class name: Arrays
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)



Constants
----------


### DEFAULT_CONJUNCTION
Default conjunction used by the conjunct() method


```php
const DEFAULT_CONJUNCTION = "and"
```





### DEFAULT_SEPARATOR
The default separator string using by joining methods


```php
const DEFAULT_SEPARATOR = ", "
```







Methods
-------


### Headzoo\Core\Arrays::remove
Removes zero or more elements from an array

Searches the array and removes every element that matches the given $needle. A non-strict
comparison (==) is made between the needle and array element unless $strict (===) is set to
true. The array will be re-index after removing items unless $preserve_keys is true.

The array is passed by reference, and may be changed. Returns the number of elements that
were removed, or 0 when the needle was not found.

Examples:
```php
$array = [
     "headzoo",
     "joe",
     "sam",
     "headzoo"
];

$removed = Arrays::remove($array, "amy");
var_dump($removed);
// Outputs: 0

$removed = Arrays::remove($array, "headzoo");
var_dump($removed);
// Outputs: 2
```
```php
public int Headzoo\Core\Arrays::remove(array $array, mixed $needle, bool $strict, bool $preserve_keys)
```

* This method is **static**.

##### Arguments

* $array **array** - The array to search
* $needle **mixed** - The needle to find
* $strict **bool** - Whether to use strict comparison
* $preserve_keys **bool** - Whether or not the array keys should be preserved



### Headzoo\Core\Arrays::containsKeyValue
Returns true if the $array contains the key $key with the value $value

Searches array $array for the key $key, and returns true if the key is found,
and the value of the key is $value. Returns false if the key does not exist, or
the key value does not equal $value.

By default the array is assumed to be multidimensional, but will be checked as a
flat array when false.

Examples:
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

$is = Arrays::containsKeyValue($arr, "headzoo", "sean@headzoo.io");
var_dump($is);

// Outputs: bool(true)

$is = Arrays::containsKeyValue($arr, "headzoo", "joe@headzoo.io");
var_dump($is);

// Outputs: bool(false)

$is = Arrays::containsKeyValue($arr, "amy", "amy@headzoo.io");
var_dump($is);

// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Arrays::containsKeyValue(array $array, string $key, mixed $value, bool $multi)
```

* This method is **static**.

##### Arguments

* $array **array** - The array to scan
* $key **string** - The key to find
* $value **mixed** - The key value
* $multi **bool** - Is $array a multidimensional array?



### Headzoo\Core\Arrays::column
Returns an array of column values from $array

Returns an array of values from a multidimensional array where the
key equals $column. Similar to the way databases can return a list of
column values from a list of matched rows.

Example:
```php
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
```php
public array Headzoo\Core\Arrays::column(array $array, string $column)
```

* This method is **static**.

##### Arguments

* $array **array** - The array with values
* $column **string** - The column name



### Headzoo\Core\Arrays::columnFilter
Filters columns of an array using a callback function

Similar to the column() method, this method returns an array of values
from a multidimensional array where the key equals $column and the list
of column values filtered by a callback function. Each element in $array
is passed to the callback function and the callback returns true to keep
the element, or false to remove it.

The callback function will receive each item in the array as the first
argument, and the array index/key as the second argument.

Example:
```php
 $a = [
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
 $ret = Arrays::columnFilter($a, "username", function($element) { return $element["admin"]; });

// Outputs: ["headzoo", "sam"]
```
```php
public array Headzoo\Core\Arrays::columnFilter(array $array, string $column, callable $callback)
```

* This method is **static**.

##### Arguments

* $array **array** - Multidimensional array
* $column **string** - Name of the column
* $callback **callable** - Filtering function



### Headzoo\Core\Arrays::join
Joins the elements of an array using an optional callback

Works exactly the same as php's join() method, however each element of
the array will be passed to the callback function. The callback return
value is what gets joined.

Example:
```php
$array = [
     "headzoo",
     "joe",
     "sam"
];

echo Arrays::join($array);
// Outputs: headzoo, joe, sam

echo Arrays::join($array, " - ");
// Outputs: headzoo - joe - sam

echo Arrays::join($array, ", ", 'Headzoo\Core\Strings::quote');
// Outputs: 'headzoo', 'joe', 'sam'

// The default separator will be used when the middle argument is omitted, and the
// last argument is callable object.
echo Arrays::join($array, 'Headzoo\Core\Strings::quote');
```
```php
public string Headzoo\Core\Arrays::join(array $array, string $separator, callable $callback)
```

* This method is **static**.

##### Arguments

* $array **array** - The array to join
* $separator **string** - The separator string
* $callback **callable** - Callback applied to each element of the array



### Headzoo\Core\Arrays::conjunct
Joins an array of values with a final conjunction

Similar to the Arrays::join() method, this method combines the array values using the default separator,
and joins the final item in the array with a conjunction. An array of strings can be turned into
a list of items, for example ["food", "water", "shelter"] becomes "food, water, and shelter".

Examples:
```php
$array = [
     "headzoo",
     "joe",
     "sam"
];

echo Arrays::conjunct($array);
// Outputs: headzoo, joe, and sam

echo Arrays::conjunct($array, "and", 'Headzoo\Core\Strings::quote');
// Outputs: 'headzoo', 'joe', and 'sam'

// The default conjunction will be used when the middle argument is omitted, and the
// last argument is callable object.
echo Arrays::conjunct($array, 'Headzoo\Core\Strings::quote');
```
```php
public string Headzoo\Core\Arrays::conjunct(array $array, string $conjunction, callable $callback)
```

* This method is **static**.

##### Arguments

* $array **array** - The array of values to join
* $conjunction **string** - The conjunction word to use
* $callback **callable** - Optional callback applied to each element of the array



### Headzoo\Core\Arrays::findString
Finds the first or last occurrence of a string within an array

Similar to the array_search() function, this method only searches for strings, and
does so in a case-insensitive manner. The value of $needle may be any type which is
castable to a string. An E_USER_WARNING is triggered if the value can't be cast to a string.

Finds the first occurrence of the needle when $reverse is false. Otherwise the method finds
the last occurrence of the needle.

Returns the array index where the string was found, or false if the string was not
found.

Examples:
```php
$arr = [
     "headzoo",
     "joe",
     "sam",
     "headzoo"
];

$index = Arrays::findString($arr, "headzoo");
echo $index;

// Outputs: 0

$index = Arrays::findString($arr, "same");
echo $index;

// Outputs: 2

$index = Arrays::findString($arr, "headzoo", true);
echo $index;

// Outputs: 4
```
```php
public mixed Headzoo\Core\Arrays::findString(array $array, mixed $needle, bool $reverse)
```

* This method is **static**.

##### Arguments

* $array **array** - The array to search
* $needle **mixed** - The string value to find
* $reverse **bool** - Return the last occurrence of the needle



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\Arrays::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\Arrays::getNamespaceName()
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
protected mixed Headzoo\Core\Arrays::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\Arrays::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


