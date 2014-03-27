Headzoo\Utilities\ValidatorInterface
===============

Interface for classes which perform data validation.




* Interface name: ValidatorInterface
* Namespace: Headzoo\Utilities
* This is an **interface**






Methods
-------


### Headzoo\Utilities\ValidatorInterface::validateRequired
Throws an exception when required values are missing from an array of key/value pairs

The $values argument is an array of key/value pairs, and the $required argument is an array
of keys which must exist in $values to validate. When $allowEmpty is false, the required values
must also evaluate to a non-empty value to validate.

This method always returns true, but throws an exception when the value is invalid.
```php
public bool Headzoo\Utilities\ValidatorInterface::validateRequired(array $values, array $required, bool $allowEmpty)
```


##### Arguments

* $values **array** - The values to validate
* $required **array** - List of keys
* $allowEmpty **bool** - Are empty values acceptable?


