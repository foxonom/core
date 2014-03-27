Headzoo\Utilities\Complete
===============

Used to call a function when the object destructs.

The class wraps a callable function, which is called in the class destructor. The utility
of this scheme is the ability to ensure the function is called eventually. Usually when
the Complete object goes out of scope, which is when it's destructor is called.

This class can be used to simulate a try...catch...finally in versions of PHP which do not
support the finally clause.

Example:
```php
// In this example the database connection will always be closed, even if the $database->fetch()
// method throws an exception, because the anonymous function passed to Complete::factory()
// is called when the $complete object goes out of scope.

$database = new FakeDatabase();
$complete = Complete::factory(function() use($database) {
     $database->close();
});
try {
     $rows = $database->fetch();
} catch (Exception $e) {
     echo $e->getTraceAsString();
     throw $e;
}
```


* Class name: Complete
* Namespace: Headzoo\Utilities
* Parent class: [Headzoo\Utilities\Core](Headzoo-Utilities-Core.md)





Properties
----------


### $callable
Function to be called in the destructor


```php
private callable $callable
```



Methods
-------


### Headzoo\Utilities\Complete::factory
Static factory class to create a new instance of this class

Example:
```php
$complete = Complete::factory(function() {
     echo "I'm complete!";
});
```
```php
public Headzoo\Utilities\Complete Headzoo\Utilities\Complete::factory(callable $callable)
```

* This method is **static**.

##### Arguments

* $callable **callable** - The function to call in the Complete destructor



### Headzoo\Utilities\Complete::__construct
Constructor


```php
public mixed Headzoo\Utilities\Complete::__construct(callable $callable)
```


##### Arguments

* $callable **callable** - The function to call in the destructor



### Headzoo\Utilities\Complete::__destruct
Destructor

Calls the set $callable function
```php
public mixed Headzoo\Utilities\Complete::__destruct()
```




### Headzoo\Utilities\Complete::__invoke
When calling an instance of this class as a function

Example:
```php
$complete = Complete::factory(function() {
     echo "I'm complete!";
});
$complete();
```
```php
public bool Headzoo\Utilities\Complete::__invoke()
```




### Headzoo\Utilities\Complete::invoke
Calls the set $callable function

Ensures the callable cannot be called twice, even if the callback has an error. Subsequent calls to this method
do nothing. Returns true when the callable was called, and false if not.

Example:
```php
$complete = Complete::factory(function() {
     echo "I'm complete!";
});
$complete->invoke();
```
```php
public bool Headzoo\Utilities\Complete::invoke()
```




### Headzoo\Utilities\Core::className
Returns the name of the class


```php
public string Headzoo\Utilities\Complete::className()
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
protected mixed Headzoo\Utilities\Complete::throwException(string $exception, string $message, int $code)
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
private string Headzoo\Utilities\Complete::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


