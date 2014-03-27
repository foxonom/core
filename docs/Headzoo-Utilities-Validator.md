Headzoo\Utilities\Validator
===============

Performs simple validation on values.




* Class name: Validator
* Namespace: Headzoo\Utilities
* This class implements: [Headzoo\Utilities\ValidatorInterface](Headzoo-Utilities-ValidatorInterface.md)




Properties
----------


### $thrownException
The type of exception thrown when a validation fails


```php
protected string $thrownException = self::DEFAULT_THROWN_EXCEPTION
```



Methods
-------


### Headzoo\Utilities\Validator::setThrownException
Sets the default thrown exception class name


```php
public mixed Headzoo\Utilities\Validator::setThrownException($thrownException)
```


##### Arguments

* $thrownException **mixed**



### Headzoo\Utilities\Validator::validateRequired
Throws an exception when required values are missing from an array of key/value pairs

The $values argument is an array of key/value pairs, and the $required argument is an array
of keys which must exist in $values to validate. When $allowEmpty is false, the required values
must also evaluate to a non-empty value to validate.

Use the Validator::setThrownException() method to set which type of exception is thrown.

This method always returns true, but throws an exception when the value is invalid.
```php
public mixed Headzoo\Utilities\Validator::validateRequired(array $values, array $required, $allowEmpty)
```


##### Arguments

* $values **array**
* $required **array**
* $allowEmpty **mixed**



### Headzoo\Utilities\Validator::throwException
Throws the configured validation exception

Examples:
```php
$validator = new Validator();
$validator->throwException("There was a serious site error!");
$validator->throwException("There was a serious site error!", 666);
$validator->throwException("There was a %s %s error!", 666, "serious", "site");

// The middle argument may be omitted when the next argument is not an integer.
$validator->throwException("There was a %s %s error!", "serious", "site");
```
```php
protected mixed Headzoo\Utilities\Validator::throwException(string $message, int $code)
```


##### Arguments

* $message **string** - The error message
* $code **int** - The error code, defaults to 0



### Headzoo\Utilities\ValidatorInterface::setThrownException
Sets the default thrown exception class name


```php
public Headzoo\Utilities\ValidatorInterface Headzoo\Utilities\ValidatorInterface::setThrownException(string $thrownException)
```

* This method is defined by [Headzoo\Utilities\ValidatorInterface](Headzoo-Utilities-ValidatorInterface.md)

##### Arguments

* $thrownException **string** - Name of an Exception class to throw



### Headzoo\Utilities\ValidatorInterface::validateRequired
Throws an exception when required values are missing from an array of key/value pairs

The $values argument is an array of key/value pairs, and the $required argument is an array
of keys which must exist in $values to validate. When $allowEmpty is false, the required values
must also evaluate to a non-empty value to validate.

Use the Validator::setThrownException() method to set which type of exception is thrown.

This method always returns true, but throws an exception when the value is invalid.
```php
public bool Headzoo\Utilities\ValidatorInterface::validateRequired(array $values, array $required, bool $allowEmpty)
```

* This method is defined by [Headzoo\Utilities\ValidatorInterface](Headzoo-Utilities-ValidatorInterface.md)

##### Arguments

* $values **array** - The values to validate
* $required **array** - List of keys
* $allowEmpty **bool** - Are empty values acceptable?


