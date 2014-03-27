Headzoo\Utilities\Functions
===============

Contains static methods for working with functions and methods.




* Class name: Functions
* Namespace: Headzoo\Utilities
* Parent class: [Headzoo\Utilities\Core](Headzoo-Utilities-Core.md)







Methods
-------


### Headzoo\Utilities\Functions::swapCallable
Swaps two variables when the second is a callable object

Used to create functions/methods which have callbacks as the final argument, and
it's desirable to make middle argument optional, while the callback remains the
final argument.

Returns true if the arguments were swapped, false if not.

Examples:
```php
function joinArray(array $values, $separator, callable $callback = null)
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
```php
public bool Headzoo\Utilities\Functions::swapCallable(mixed $optional, mixed $callable, mixed $default)
```

* This method is **static**.

##### Arguments

* $optional **mixed** - The optional argument
* $callable **mixed** - Possibly a callable object
* $default **mixed** - The optional argument default value



### Headzoo\Utilities\Core::className
Returns the name of the class


```php
public string Headzoo\Utilities\Functions::className()
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
protected mixed Headzoo\Utilities\Functions::throwException(string $exception, string $message, int $code)
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
private string Headzoo\Utilities\Functions::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


