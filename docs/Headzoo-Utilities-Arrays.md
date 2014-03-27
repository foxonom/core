Headzoo\Utilities\Arrays
===============

Contains static methods for working with arrays.




* Class name: Arrays
* Namespace: Headzoo\Utilities
* Parent class: [Headzoo\Utilities\Core](Headzoo-Utilities-Core.md)



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


### Headzoo\Utilities\Arrays::containsKeyValue
Returns true if the $array contains the key $key with the value $value

Searches array $array for the key $key, and returns true if the key is found,
and the value of the key is $value. Returns false if the key does not exist, or
the key value does not equal $value.
```php
public bool Headzoo\Utilities\Arrays::containsKeyValue(array $array, string $key, mixed $value, bool $multi)
```

* This method is **static**.

##### Arguments

* $array **array** - The array to scan
* $key **string** - The key to find
* $value **mixed** - The key value
* $multi **bool** - Is $array a multidimensional array?



### Headzoo\Utilities\Arrays::column
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
```

Returns:
 `["headzoo", "joe"]`
```php
public array Headzoo\Utilities\Arrays::column(array $array, string $column)
```

* This method is **static**.

##### Arguments

* $array **array** - The array with values
* $column **string** - The column name



### Headzoo\Utilities\Arrays::columnFilter
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
```

Returns:
 `["headzoo", "sam"]`
```php
public array Headzoo\Utilities\Arrays::columnFilter(array $array, string $column, callable $callback)
```

* This method is **static**.

##### Arguments

* $array **array** - Multidimensional array
* $column **string** - Name of the column
* $callback **callable** - Filtering function



### Headzoo\Utilities\Arrays::join
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

echo Arrays::join($array, ", ", 'Headzoo\Utilities\Strings::quote');
// Outputs: 'headzoo', 'joe', 'sam'

// The default separator will be used when the middle argument is omitted, and the
// last argument is callable object.
echo Arrays::join($array, 'Headzoo\Utilities\Strings::quote');
```
```php
public string Headzoo\Utilities\Arrays::join(array $array, string $separator, callable $callback)
```

* This method is **static**.

##### Arguments

* $array **array** - The array to join
* $separator **string** - The separator string
* $callback **callable** - Callback applied to each element of the array



### Headzoo\Utilities\Arrays::conjunct
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

echo Arrays::conjunct($array, "and", 'Headzoo\Utilities\Strings::quote');
// Outputs: 'headzoo', 'joe', and 'sam'

// The default conjunction will be used when the middle argument is omitted, and the
// last argument is callable object.
echo Arrays::conjunct($array, 'Headzoo\Utilities\Strings::quote');
```
```php
public string Headzoo\Utilities\Arrays::conjunct(array $array, string $conjunction, callable $callback)
```

* This method is **static**.

##### Arguments

* $array **array** - The array of values to join
* $conjunction **string** - The conjunction word to use
* $callback **callable** - Optional callback applied to each element of the array



### Headzoo\Utilities\Arrays::findString
Finds the first or last occurrence of a string within an array

Similar to the array_search() function, this method only searches for strings, and
does so in a case-insensitive manner. The value of $needle may be any type which is
castable to a string. An E_USER_WARNING is triggered if the value can't be cast to a string.

Finds the first occurrence of the needle when $reverse is false. Otherwise the method finds
the last occurrence of the needle.

Returns the array index where the string was found, or false if the string was not
found.
```php
public mixed Headzoo\Utilities\Arrays::findString(array $array, mixed $needle, bool $reverse)
```

* This method is **static**.

##### Arguments

* $array **array** - The array to search
* $needle **mixed** - The string value to find
* $reverse **bool** - Return the last occurrence of the needle



### Headzoo\Utilities\Core::className
Returns the name of the class


```php
public string Headzoo\Utilities\Arrays::className()
```




### Headzoo\Utilities\Core::throwException
Throws the configured validation exception

Available place holders:
 {me}        - The name of the class throwing the exception
 {exception} - The name of the exception being thrown
 {code}      - The exception code
 {date}      - The date the exception was thrown

Examples:
```php
$validator = new Validator();
$validator->throwException("There was a serious site error!");
$validator->throwException("There was a serious site error!", 666);
$validator->throwException("There was a {0} {1} error!", 666, "serious", "site");

// The middle argument may be omitted when the next argument is not an integer.
$validator->throwException("There was a {0} {1} error!", "serious", "site");
```
```php
protected mixed Headzoo\Utilities\Arrays::throwException(string $exception, string $message, int $code)
```

* This method is **static**.

##### Arguments

* $exception **string** - The name of the exception to throw
* $message **string** - The error message
* $code **int** - The error code, defaults to 0



### Headzoo\Utilities\Core::interpolate
Interpolates context values into the message placeholders.

Taken from PSR-3's example implementation.
```php
private string Headzoo\Utilities\Arrays::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


