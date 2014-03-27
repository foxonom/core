Headzoo\Utilities\Validator
===============

Performs simple validation on values.




* Class name: Validator
* Namespace: Headzoo\Utilities
* Parent class: [Headzoo\Utilities\Core](Headzoo-Utilities-Core.md)
* This class implements: [Headzoo\Utilities\ValidatorInterface](Headzoo-Utilities-ValidatorInterface.md)






Methods
-------


### Headzoo\Utilities\Validator::validateRequired
Throws an exception when required values are missing from an array of key/value pairs

The $values argument is an array of key/value pairs, and the $required argument is an array
of keys which must exist in $values to validate. When $allowEmpty is false, the required values
must also evaluate to a non-empty value to validate.

This method always returns true, but throws an exception when the value is invalid.
```php
public mixed Headzoo\Utilities\Validator::validateRequired(array $values, array $required, $allowEmpty)
```


##### Arguments

* $values **array**
* $required **array**
* $allowEmpty **mixed**



### Headzoo\Utilities\Core::className
Returns the name of the class


```php
public string Headzoo\Utilities\Validator::className()
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
protected mixed Headzoo\Utilities\Validator::throwException(string $exception, string $message, int $code)
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
private string Headzoo\Utilities\Validator::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message



### Headzoo\Utilities\ValidatorInterface::validateRequired
Throws an exception when required values are missing from an array of key/value pairs

The $values argument is an array of key/value pairs, and the $required argument is an array
of keys which must exist in $values to validate. When $allowEmpty is false, the required values
must also evaluate to a non-empty value to validate.

This method always returns true, but throws an exception when the value is invalid.
```php
public bool Headzoo\Utilities\ValidatorInterface::validateRequired(array $values, array $required, bool $allowEmpty)
```

* This method is defined by [Headzoo\Utilities\ValidatorInterface](Headzoo-Utilities-ValidatorInterface.md)

##### Arguments

* $values **array** - The values to validate
* $required **array** - List of keys
* $allowEmpty **bool** - Are empty values acceptable?


