Headzoo\Utilities\ValidatorInterface
===============

Interface for classes which perform data validation.




* Interface name: ValidatorInterface
* Namespace: Headzoo\Utilities
* This is an **interface**


Constants
----------


### DEFAULT_THROWN_EXCEPTION
The default type of exception thrown when a validation fails


```php
const DEFAULT_THROWN_EXCEPTION = \Headzoo\Utilities\Exceptions\ValidationFailedException::class
```







Methods
-------


### Headzoo\Utilities\ValidatorInterface::setThrownException
Sets the default thrown exception class name


```php
public Headzoo\Utilities\ValidatorInterface Headzoo\Utilities\ValidatorInterface::setThrownException(string $thrownException)
```


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


##### Arguments

* $values **array** - The values to validate
* $required **array** - List of keys
* $allowEmpty **bool** - Are empty values acceptable?


