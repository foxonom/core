Headzoo\Utilities\Functions
===============

Contains static methods for working with functions and methods.




* Class name: Functions
* Namespace: Headzoo\Utilities







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


