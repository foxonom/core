Headzoo\Core\ConfigurableCallable
===============

Creates a callable instance with configurable behavior.

Primarily used when you need to continuously call a method or function until certain
conditions are met. For example until an exception isn't being thrown, or a specific
value is returned.

#### Examples
In this example you want to insert a row into the database, which may lead to
a DeadLockException being thrown. The recommended action for dead locks is retrying
the query. We use a ConfigurableCallable instance to keep trying the query until
it succeeds.

```php
// Establish a link to the database, and create a callable wrapping the
// mysqli_query() function.
$link  = mysqli_connect("localhost", "my_user", "my_password", "my_db");
$query = new ConfigurableCallable("mysqli_query");

// The retryOnException(DeadLockException:class) call tells $query to keep calling
// mysqli_query($link, "INSERT INTO `members` ('headzoo')") until DeadLockException
// is no longer being thrown. As many times as needed, or until the $query->max_retries
// (defaults to 10) value is reached.
$query->retryOnException(DeadLockException::class);
$result = $query($link, "INSERT INTO `members` ('headzoo')");
```

In this example we will call a remote web API, which sometimes takes a few tries
depending on how busy the remote server is at the any given moment. The remote
server may return an empty value (null), the API library may thrown an exception,
or PHP may trigger an error.

```php
$api     = new RemoteApi();
$members = new ConfigurableCallable([$api, "getMembers"]);

// When calling retryOnException() without the name of a specified exception,
// the callable will keep retrying when any kind of exception is thrown.
$members->retryOnException()
        ->retryOnError()
        ->retryOnNull();

// The $members instance will keep trying to call $api->getMembers(0, 10) until
// an exception is no longer being thrown, PHP is not triggering any errors,
// and the remote server is not returning null.
$rows = $members(0, 10);
```

The ConfigurableCallable::setMaxRetries() method is used to limit the number of
times before the callable gives up. If the callable does give up, it will return
the last value returned by the wrapped function, or throw the last exception
thrown by the function. PHP errors are converted to exceptions and thrown.
Errors can be logged by passing a psr\Log\LoggerInterface instance to the
ConfigurableCallable constructor.

```php
$link  = mysqli_connect("localhost", "my_user", "my_password", "my_db");
$query = new ConfigurableCallable("mysqli_query", new FileLogger());
$query->retryOnException(DeadLockException::class);
$query->setMaxRetries(5);

// The $query instance will throw the last caught DeadLockException if the
// max retries value is reached without successfully inserting the row.
try {
     $result = $query($link, "INSERT INTO `members` ('headzoo')");
} catch (DeadLockException $e) {
     die("Could not insert row.");
}
```

There are dozens of different configuration options, which are listed below.
In this example we keep calling a function until it returns a value greater
than 42. We'll pass -1 to the setMaxRetries() method, which means retry an
unlimited number of times.

```php
$counter = ConfigurableCallable::factory(function() {
     static $count = 0;
     return ++$count;
});
$counter->setMaxRetries(-1)
        ->retryOnLessThan(42);
echo $counter();
// Outputs: 42
```

In addition to the retry conditions, there are also return conditions, and throw
conditions. In this example we want to call a remote API until it returns true or
false.

```php
$api     = new RemoteApi();
$members = new ConfigurableCallable([$api, "doesMemberExist"]);
$members->returnOnTrue()
        ->returnOnFalse();
$does_exist = $members("headzoo");
```

These are the methods which are available.


* Class name: ConfigurableCallable
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)
* This class implements: psr\Log\LoggerAwareInterface


Constants
----------


### DEFAULT_MAX_RETRIES
Default maximum number of retries


```php
const DEFAULT_MAX_RETRIES = 10
```





Properties
----------


### $actions
The possible actions


```php
private array $actions = array("retry", "return", "throw")
```

* This property is **static**.


### $expressions
The possible expressions


```php
private array $expressions = array("Exception" => array("req_arg" => true, "default" => \Exception::class, "actions" => array("retry", "return", "throw"), "compare" => "isExceptionOf"), "Error" => array("req_arg" => false, "default" => \Headzoo\Core\Exceptions\PHPException::class, "actions" => array("retry", "return", "throw"), "compare" => "isExceptionOf"), "Null" => array("req_arg" => false, "default" => null, "actions" => array("retry", "return"), "compare" => "isStrictlyEquals"), "True" => array("req_arg" => false, "default" => true, "actions" => array("retry", "return"), "compare" => "isStrictlyEquals"), "False" => array("req_arg" => false, "default" => false, "actions" => array("retry", "return"), "compare" => "isStrictlyEquals"), "InstanceOf" => array("req_arg" => true, "actions" => array("retry", "return"), "compare" => "isInstanceOf"), "Equals" => array("req_arg" => true, "actions" => array("retry", "return"), "compare" => "isEquals"), "NotEquals" => array("req_arg" => true, "actions" => array("retry", "return"), "compare" => "isNotEquals"), "GreaterThan" => array("req_arg" => true, "actions" => array("retry", "return"), "compare" => "isGreaterThan"), "GreaterThanOrEquals" => array("req_arg" => true, "actions" => array("retry", "return"), "compare" => "isGreaterThanOrEquals"), "LessThan" => array("req_arg" => true, "actions" => array("retry", "return"), "compare" => "isLessThan"), "LessThanOrEquals" => array("req_arg" => true, "actions" => array("retry", "return"), "compare" => "isLessThanOrEquals"))
```

* This property is **static**.


### $regex
The dynamic method regex


```php
private string $regex
```

* This property is **static**.


### $max_retries
Max number of retries


```php
protected int $max_retries = self::DEFAULT_MAX_RETRIES
```



### $callback
The wrapped callable


```php
protected callable $callback
```



### $conditions
The configured conditions


```php
protected array $conditions = array()
```



### $filter
The filter callback


```php
protected callable $filter
```



Methods
-------


### Headzoo\Core\ConfigurableCallable::factory
Factory method


```php
public Headzoo\Core\ConfigurableCallable Headzoo\Core\ConfigurableCallable::factory(callable $callback)
```

* This method is **static**.

##### Arguments

* $callback **callable** - The callback to invoke



### Headzoo\Core\ConfigurableCallable::__construct
Constructor


```php
public mixed Headzoo\Core\ConfigurableCallable::__construct(callable $callback, psr\Log\LoggerInterface $logger)
```


##### Arguments

* $callback **callable** - The callback to invoke
* $logger **psr\Log\LoggerInterface** - Used to log errors



### Headzoo\Core\ConfigurableCallable::setMaxRetries
Sets the max number of retries

The value may be set to -1 for an unlimited number of retries. Use with caution,
as you may leave your application frozen.

Example:
```php
$counter = ConfigurableCallable::factory(function() {
     static $count = 0;
     return ++$count;
});
$counter->setMaxRetries(-1)
        ->retryOnLessThan(42);
echo $counter();
// Outputs: 42
```
```php
public Headzoo\Core\ConfigurableCallable Headzoo\Core\ConfigurableCallable::setMaxRetries(mixed $max_retries)
```


##### Arguments

* $max_retries **mixed**



### Headzoo\Core\ConfigurableCallable::setFilter
Sets a return value filter

The return value from the wrapped function may be filtered through another function,
which is specified here. Note the filtering happens after all conditions have been
met, and the callable is returning a value.

Example:
```php
$func = ConfigurableCallable::factory(function() {
     return "hello world!";
});
$func->setFilter(function($str) {
     return strtoupper($str);
});
echo $func();

// Outputs: "HELLO WORLD!"
```
```php
public Headzoo\Core\ConfigurableCallable Headzoo\Core\ConfigurableCallable::setFilter(callable $filter)
```


##### Arguments

* $filter **callable**



### Headzoo\Core\ConfigurableCallable::__call



```php
public Headzoo\Core\ConfigurableCallable Headzoo\Core\ConfigurableCallable::__call(string $method, array $args)
```


##### Arguments

* $method **string** - The method being called
* $args **array** - The method arguments



### Headzoo\Core\ConfigurableCallable::__invoke
Calls the function


```php
public mixed Headzoo\Core\ConfigurableCallable::__invoke()
```




### Headzoo\Core\ConfigurableCallable::invoke
Calls the function


```php
public mixed Headzoo\Core\ConfigurableCallable::invoke()
```




### Headzoo\Core\ConfigurableCallable::matchesCondition
Returns which condition the value matches


```php
protected null|string Headzoo\Core\ConfigurableCallable::matchesCondition(mixed $return, Exception $exception)
```


##### Arguments

* $return **mixed** - The callable return value or thrown exception
* $exception **Exception** - Any exception that was thrown



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\ConfigurableCallable::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\ConfigurableCallable::getNamespaceName()
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
protected mixed Headzoo\Core\ConfigurableCallable::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\ConfigurableCallable::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message



### 
ConfigurableCallable returnOnLessThanOrEquals


```php
public  Headzoo\Core\ConfigurableCallable::()
```



