Headzoo\Core\Comparator
===============

Contains static methods for making comparisons between values.

Primarily used when a callback is needed which makes comparisons between
two values.

#### Example

```php
$arr = [
     "joe",
     "headzoo",
     "amy"
];
usort($arr, 'Headzoo\Core\Comparator::compare');
print_r($arr);

// Outputs:
// [
//   "amy",
//   "joe",
//   "headzoo"
// ]
```


* Class name: Comparator
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)







Methods
-------


### Headzoo\Core\Comparator::compare
Returns the order of the left and right hand values as an integer

Compares its two arguments for order. Returns a negative integer, zero, or a positive integer as
the first argument is less than, equal to, or greater than the second.

Example:
```php
$arr = [
     "joe",
     "headzoo",
     "amy"
];
usort($arr, 'Headzoo\Core\Comparator::compare');
print_r($arr);

// Outputs:
// [
//   "amy",
//   "joe",
//   "headzoo"
// ]
```
```php
public int Headzoo\Core\Comparator::compare(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::reverse
Returns the reverse order of the left and right hand values as an integer

Compares its two arguments for order. Returns a negative integer, zero, or a positive integer as
the first argument is greater than, equal to, or less than the second.

Example:
```php
$arr = [
     "joe",
     "headzoo",
     "amy"
];
usort($arr, 'Headzoo\Core\Comparator::reverse');
print_r($arr);

// Outputs:
// [
//   "headzoo",
//   "joe",
//   "amy"
// ]
```
```php
public int Headzoo\Core\Comparator::reverse(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isEquals
Returns whether two values are equal

Example:
```php
var_dump(Comparator::isEquals(5, 5));
// Outputs: bool(true)

var_dump(Comparator::isEquals(5, "5"));
// Outputs: bool(true)
```
```php
public bool Headzoo\Core\Comparator::isEquals(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isNotEquals
Returns whether two values are not equal

Example:
```php
var_dump(Comparator::isNotEquals(5, 5));
// Outputs: bool(false)

var_dump(Comparator::isNotEquals(5, "5"));
// Outputs: bool(false)

var_dump(Comparator::isNotEquals(15, 5));
// Outputs: bool(true)

var_dump(Comparator::isNotEquals(15, "5"));
// Outputs: bool(true)
```
```php
public bool Headzoo\Core\Comparator::isNotEquals(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isStrictlyEquals
Returns whether two values are equal using strict comparison

Example:
```php
var_dump(Comparator::isStrictlyEquals(5, 5));
// Outputs: bool(true)

var_dump(Comparator::isStrictlyEquals(5, "5"));
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Comparator::isStrictlyEquals(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isStrictlyNotEquals
Returns whether two values are not equal using strict comparison

Example:
```php
var_dump(Comparator::isStrictlyNotEquals(6, 5));
// Outputs: bool(true)

var_dump(Comparator::isStrictlyNotEquals(6, "5"));
// Outputs: bool(true)

var_dump(Comparator::isStrictlyNotEquals(5, 5));
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Comparator::isStrictlyNotEquals(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isLessThan
Returns whether the left hand value is less than the right hand value

Example:
```php
var_dump(Comparator::isLessThan(4, 5));
// Outputs: bool(true)

var_dump(Comparator::isLessThan(4, "5"));
// Outputs: bool(true)

var_dump(Comparator::isLessThan(5, 5));
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Comparator::isLessThan(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isLessThanOrEquals
Returns whether the left hand value is less than or equal to the right hand value

Example:
```php
var_dump(Comparator::isLessThanOrEquals(4, 5));
// Outputs: bool(true)

var_dump(Comparator::isLessThanOrEquals(4, "5"));
// Outputs: bool(true)

var_dump(Comparator::isLessThanOrEquals(5, 5));
// Outputs: bool(true)

var_dump(Comparator::isLessThanOrEquals(6, 5));
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Comparator::isLessThanOrEquals(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isGreaterThan
Returns whether the left hand value is greater than the right hand value

Example:
```php
var_dump(Comparator::isGreaterThan(6, 5));
// Outputs: bool(true)

var_dump(Comparator::isGreaterThan(6, "5"));
// Outputs: bool(true)

var_dump(Comparator::isGreaterThan(4, 5));
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Comparator::isGreaterThan(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isGreaterThanOrEquals
Returns whether the left hand value is greater than or equal to the right hand value

Example:
```php
var_dump(Comparator::isGreaterThanOrEquals(6, 5));
// Outputs: bool(true)

var_dump(Comparator::isGreaterThanOrEquals(5, "5"));
// Outputs: bool(true)

var_dump(Comparator::isGreaterThanOrEquals(4, 5));
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Comparator::isGreaterThanOrEquals(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Comparator::isInstanceOf
Returns whether the left hand value is an instance of the right hand value

Example:
```php
$obj = new stdClass();
var_dump(Comparator::isInstanceOf($obj, stdClass::class));
// Outputs: bool(true)

var_dump(Comparator::isInstanceOf($obj, Comparator::class));
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Comparator::isInstanceOf(mixed $left, mixed $right)
```

* This method is **static**.

##### Arguments

* $left **mixed** - The left value
* $right **mixed** - The right value



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\Comparator::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\Comparator::getNamespaceName()
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
protected mixed Headzoo\Core\Comparator::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\Comparator::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


