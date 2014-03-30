Headzoo\Core\Errors
===============

Utility class used to work with E_ERROR constants.




* Class name: Errors
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)





Properties
----------


### $errors
The error constants


```php
private array $errors = array("E_ERROR" => E_ERROR, "E_WARNING" => E_WARNING, "E_PARSE" => E_PARSE, "E_NOTICE" => E_NOTICE, "E_CORE_ERROR" => E_CORE_ERROR, "E_CORE_WARNING" => E_CORE_WARNING, "E_COMPILE_ERROR" => E_COMPILE_ERROR, "E_COMPILE_WARNING" => E_COMPILE_WARNING, "E_USER_ERROR" => E_USER_ERROR, "E_USER_WARNING" => E_USER_WARNING, "E_USER_NOTICE" => E_USER_NOTICE, "E_STRICT" => E_STRICT, "E_RECOVERABLE_ERROR" => E_RECOVERABLE_ERROR, "E_DEPRECATED" => E_DEPRECATED, "E_USER_DEPRECATED" => E_USER_DEPRECATED, "E_ALL" => E_ALL)
```

* This property is **static**.


### $errors_user
The user level error constants


```php
private array $errors_user = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_USER_DEPRECATED)
```

* This property is **static**.


Methods
-------


### Headzoo\Core\Errors::isTrueError
Returns a boolean value indicating whether an integer is a E_ERROR constant

Given an integer, the method returns true when one of the E_ERROR constants has
the same value.

Examples:
```php
$is_true = Errors::isTrueError(E_WARNING);
var_dump($is_true);
// Outputs: bool(true)

$is_true = Errors::isTrueError("E_WARNING");
var_dump($is_true);
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Errors::isTrueError(int $error)
```

* This method is **static**.

##### Arguments

* $error **int** - The value to test



### Headzoo\Core\Errors::isTrueUser
Returns a boolean value indicating whether an integer is one of the E_USER error constants

Given an integer, the method returns true when one of the E_USER_ERROR constants has
the same value.

Examples:
```php
$is_user = Errors::isTrueUser(E_USER_ERROR);
var_dump($is_user);
// Outputs: bool(true)

$is_user = Errors::isTrueUser(E_ERROR);
var_dump($is_user);
// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Errors::isTrueUser(int|string $error)
```

* This method is **static**.

##### Arguments

* $error **int|string** - The error to test



### Headzoo\Core\Errors::toInteger
Returns the value of the error as an integer

The value of $error may be either one of the E_ERROR constants, or a string naming one
of the constants. The integer value of the constant is returned, or an exception is
thrown when $error is not valid.

The $error argument may be either an integer (one of the E_ERROR values) or a string
with the name of an E_ERROR constant.

Examples:
```php
echo Errors::toInteger("E_STRICT");
// Outputs: 2048

echo Errors::toInteger(E_WARNING);
// Outputs: 2
```
```php
public int Headzoo\Core\Errors::toInteger(string|int $error)
```

* This method is **static**.

##### Arguments

* $error **string|int** - The error to convert



### Headzoo\Core\Errors::toString
Returns the value of the error as a string

Returns the string representation of the given error. For example when given an
E_WARNING, the method returns "E_WARNING".

The $error argument may be either an integer (one of the E_ERROR values) or a string
with the name of an E_ERROR constant.

Examples:
```php
echo Errors::toString(E_WARNING);
// Outputs: "E_WARNING"

echo Errors::toString("E_CORE_ERROR");
// Outputs: "E_CORE_ERROR"
```
```php
public int Headzoo\Core\Errors::toString(int|string $error)
```

* This method is **static**.

##### Arguments

* $error **int|string** - The error to convert



### Headzoo\Core\Errors::toUser
Converts an E_ERROR constant to the E_USER_ERROR equivalent

For example when given an E_WARNING constant value, the error is converted into
an E_USER_WARNING value. Not all E_ERROR constants have E_USER_ERROR equivalents.
In those cases the error remains unchanged, and the method returns false.

Returns whether the error is an E_USER_ERROR. Either before or after converting.

Examples:
```php
$error = E_ERROR;
$is_user = Errors::toUser($error);
var_dump($error);
var_dump($is_user);

// Outputs:
// 256 (E_USER_ERROR)
// bool(true)

$error = E_USER_WARNING;
$is_user = Errors::toUser($error);
var_dump($error);
var_dump($is_user);

// Outputs:
// 512 (E_USER_WARNING)
// bool(true)

$error = E_CORE_ERROR;
$is_user = Errors::toUser($error);
var_dump($error);
var_dump($is_user);

// Outputs:
// 16 (E_CORE_ERROR)
// bool(false)
```
```php
public bool Headzoo\Core\Errors::toUser(int $error)
```

* This method is **static**.

##### Arguments

* $error **int** - The error to convert



### Headzoo\Core\Errors::toArray
Returns an array of the E_ERROR constant values


```php
public int[] Headzoo\Core\Errors::toArray()
```

* This method is **static**.



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\Errors::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\Errors::getNamespaceName()
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
protected mixed Headzoo\Core\Errors::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\Errors::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


