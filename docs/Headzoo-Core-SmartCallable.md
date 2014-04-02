Headzoo\Core\SmartCallable
===============

Used to call a function when a resource is no longer needed.

SmartCallable instances wrap functions, which are called when the SmartCallable instances
go out of scope. The idea is similar to "smart pointers" from C++, where an object -- the smart
pointer -- wraps a resource, and automatically frees the resource when it's no longer needed.

In this example we create a method which requests a web resource using curl.
We use a SmartCallable instance to ensure the curl resource is closed when
the method returns, or an exception is thrown.

```php
public function fetch()
{
     $curl = curl_init("http://some-site.com");
     $sc = SmartCallable::factory(function() use($curl) {
             curl_close($curl);
     });

     $response = curl_exec($curl);
     if ($e = curl_error()) {
         throw new Exception($e);
     }

     return $response;
}
```

The method could also be written this way.

```php
public function fetch()
{
     $curl = curl_init("http://some-site.com");
     $sc = SmartCallable::factory("curl_close", $curl);

     $response = curl_exec($curl);
     if ($e = curl_error()) {
         throw new Exception($e);
     }

     return $response;
}
```
This class can be used to simulate a try...catch...finally in versions of PHP which do not
support the finally clause. In this example the database connection is closed even if we
throw an exception before calling the close() method.

```php
public function fetchRows()
{
     $mysqli = new mysqli('localhost', 'my_user', 'my_password', 'my_db');
     $sc = SmartCallable::factory(function() use($mysql) {
         $mysqli->close();
     });

     try {
         $result = $mysqli->query("SELECT Name FROM City LIMIT 10");
     } catch (Exception $e) {
         $this->logger->error($e->getMessage());
         throw $e;
     }

     $mysql->close();
     // We could also close the connection by invoking the SmartCallable object.
     $sc();

     return $result;
}
```


* Class name: SmartCallable
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)





Properties
----------


### $callable
Function to be called in the destructor


```php
private callable $callable
```



### $args
Arguments passed to the callable


```php
private array $args = array()
```



Methods
-------


### Headzoo\Core\SmartCallable::factory
Static factory class to create a new instance of this class

Example:
```php
$sc = SmartCallable::factory(function() {
     echo "I'm complete!";
});

// Passing arguments to the callable.
$sc = SmartCallable::factory(function($arg1, $arg1) {
     echo "{$arg1}, {$arg2}";
}, "Hello", "World");

// Using a string as the callable, and passing arguments.
$curl = curl_init("http://some-site.com");
$sc = SmartCallable::factory("curl_close", $curl);
```
```php
public Headzoo\Core\SmartCallable Headzoo\Core\SmartCallable::factory(callable $callable, mixed $args)
```

* This method is **static**.

##### Arguments

* $callable **callable** - The function to call in the Complete destructor
* $args **mixed** - Arguments passed to the callable



### Headzoo\Core\SmartCallable::__construct
Constructor


```php
public mixed Headzoo\Core\SmartCallable::__construct(callable $callable, array $args)
```


##### Arguments

* $callable **callable** - The function to call in the destructor
* $args **array** - Arguments passed to the callable



### Headzoo\Core\SmartCallable::__destruct
Destructor

Calls the set $callable function
```php
public mixed Headzoo\Core\SmartCallable::__destruct()
```




### Headzoo\Core\SmartCallable::__invoke
When calling an instance of this class as a function

Example:
```php
$sc = SmartCallable::factory(function() {
     echo "I'm complete!";
});
$sc();
```
```php
public bool Headzoo\Core\SmartCallable::__invoke()
```




### Headzoo\Core\SmartCallable::invoke
Calls the set $callable function

Ensures the callable cannot be called twice, even if the callback has an error. Subsequent calls to this method
do nothing. Returns true when the callable was called, and false if not.

Example:
```php
$sc = SmartCallable::factory(function() {
     echo "I'm complete!";
});
$sc->invoke();
```
```php
public bool Headzoo\Core\SmartCallable::invoke()
```




### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\SmartCallable::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\SmartCallable::getNamespaceName()
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
protected mixed Headzoo\Core\SmartCallable::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\SmartCallable::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


