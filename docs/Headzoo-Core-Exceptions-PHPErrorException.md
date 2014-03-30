Headzoo\Core\Exceptions\PHPErrorException
===============

Thrown when PHP triggers an error.




* Class name: PHPErrorException
* Namespace: Headzoo\Core\Exceptions
* Parent class: [Headzoo\Core\Exceptions\Exception](Headzoo-Core-Exceptions-Exception.md)







Methods
-------


### Headzoo\Core\Exceptions\PHPErrorException::__construct
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


