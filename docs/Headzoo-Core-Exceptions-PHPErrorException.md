Headzoo\Core\Exceptions\PHPErrorException
===============

Thrown when PHP triggers an E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR, E_CORE_ERROR or E_COMPILE_ERROR.




* Class name: PHPErrorException
* Namespace: Headzoo\Core\Exceptions
* Parent class: [Headzoo\Core\Exceptions\PHPException](Headzoo-Core-Exceptions-PHPException.md)







Methods
-------


### Headzoo\Core\Exceptions\PHPException::factory
Returns an exception for the given code

The value of $code should be one of the E_ERROR constants. This method will return the
correct PHPException instance for that code. For example, if $code == E_WARNING, an
instance of PHPWarningException is returned.
```php
public Headzoo\Core\Exceptions\PHPException Headzoo\Core\Exceptions\PHPErrorException::factory(string $message, int $code, string $file, int $line, Exception $prev)
```

* This method is **static**.

##### Arguments

* $message **string** - The error message
* $code **int** - The error code
* $file **string** - The file where the error occurred
* $line **int** - The line in the file where the error occurred
* $prev **Exception** - The previous exception



### Headzoo\Core\Exceptions\PHPException::__construct
Constructor


```php
public mixed Headzoo\Core\Exceptions\PHPErrorException::__construct(string $message, int $code, string $file, int $line, Exception $prev)
```


##### Arguments

* $message **string** - The error message
* $code **int** - The error code
* $file **string** - The file where the error occurred
* $line **int** - The line where the error occurred
* $prev **Exception** - Previous exception


