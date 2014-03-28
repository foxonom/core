Headzoo\Core\Complete
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
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)





Properties
----------


### $callable
Function to be called in the destructor


```php
private callable $callable
```



Methods
-------


### Headzoo\Core\Complete::factory
Static factory class to create a new instance of this class

Example:
```php
$complete = Complete::factory(function() {
     echo "I'm complete!";
});
```
```php
public Headzoo\Core\Complete Headzoo\Core\Complete::factory(callable $callable)
```

* This method is **static**.

##### Arguments

* $callable **callable** - The function to call in the Complete destructor



### Headzoo\Core\Complete::__construct
Constructor


```php
public mixed Headzoo\Core\Complete::__construct(callable $callable)
```


##### Arguments

* $callable **callable** - The function to call in the destructor



### Headzoo\Core\Complete::__destruct
Destructor

Calls the set $callable function
```php
public mixed Headzoo\Core\Complete::__destruct()
```




### Headzoo\Core\Complete::__invoke
When calling an instance of this class as a function

Example:
```php
$complete = Complete::factory(function() {
     echo "I'm complete!";
});
$complete();
```
```php
public bool Headzoo\Core\Complete::__invoke()
```




### Headzoo\Core\Complete::invoke
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
public bool Headzoo\Core\Complete::invoke()
```




### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\Complete::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\Complete::getNamespaceName()
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
protected mixed Headzoo\Core\Complete::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\Complete::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


