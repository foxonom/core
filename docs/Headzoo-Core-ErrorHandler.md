Headzoo\Core\ErrorHandler
===============

Used to capture and handle core PHP errors and uncaught exceptions.

#### Examples

The most basic setup, this will handle errors which crash scripts by displaying
HTML5 with the error message and backtrace.

```php
$handler = new ErrorHandler();
$handler->handle();
```

That's all there is too it. That may be fine during development, but in production you
will probably want to display
your own error page. (One that doesn't show sensitive information) You do that by
registering your own error callback instead of using the default, which will be called when an error
is captured. You can do whatever you want in that function: include an error page, email
yourself the error, etc.

```php
$handler = new ErrorHandler();
$handler->setCallback(function($handler) {
     // The $handler parameter is the ErrorHandler instance.
     // The $handler->getLastError() method returns an exception which
     // describes the error.
     include("templates/error.php");
});

$handler->handle();
```

That's looking better, but you may want to handle errors differently in production and
development. For that situation the ErrorHandler class supports the use of "environments".
You tell the handler which environment -- "live", "staging", "development" -- is currently
running, and then defined different callbacks for each possible environment.

```php
if (!defined("ENVIRONMENT")) {
     define("ENVIRONMENT", "live");
}

$handler = new ErrorHandler();
$handler->setCallback("live", function($handler) {
     include("templates/live_error.php");
});
$handler->setCallback("dev", function($handler) {
     include("templates/dev_error.php");
});

$handler->handle(ENVIRONMENT);
```

We pass the currently running environment to the ErrorHandler::handle() method. When an error is
trapped, the callback set for that environment will be called.

There are many more options for dealing with how errors are handled, and which errors are
handled. See the API documentation for more information.

Notes:
The instances of this class stop handling errors when the instance goes out of scope. Therefore the
instance should be created in the global scope.

Only the single error is handled, because the errors it handles are meant to kill execution
of the script. The callbacks should handle a graceful shutdown.


* Class name: ErrorHandler
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)
* This class implements: psr\Log\LoggerAwareInterface


Constants
----------


### DEFAULT_ENVIRONMENT
The default runtime environment


```php
const DEFAULT_ENVIRONMENT = "development"
```





Properties
----------


### $default_errors
The default types of errors handled for every runtime environment


```php
private int[] $default_errors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_USER_ERROR, E_USER_WARNING, E_RECOVERABLE_ERROR)
```

* This property is **static**.


### $default_exceptions
The default types of exceptions handled for every runtime environment


```php
private string[] $default_exceptions = array(\Exception::class)
```

* This property is **static**.


### $running_env
The running environment


```php
protected string $running_env
```



### $last_error
The last generated exception


```php
protected  $last_error
```



### $is_handling
Whether the error handler has been activated


```php
protected bool $is_handling = false
```



### $callbacks
Called when an error is handled


```php
protected array $callbacks = array()
```



### $errors
Core errors which are being handled


```php
protected array $errors = array()
```



### $exceptions
Exceptions which are being handled


```php
protected array $exceptions = array()
```



### $prev_error_handler
The previously registered error handler


```php
protected callable $prev_error_handler
```



### $prev_exception_handler
The previously registered uncaught exception handler


```php
protected callable $prev_exception_handler
```



Methods
-------


### Headzoo\Core\ErrorHandler::getDefaultUncaughtExceptions
Returns the types of uncaught exceptions which are handled by default


```php
public string[] Headzoo\Core\ErrorHandler::getDefaultUncaughtExceptions()
```

* This method is **static**.



### Headzoo\Core\ErrorHandler::getDefaultCoreErrors
Returns the core errors which are handled by default


```php
public int[] Headzoo\Core\ErrorHandler::getDefaultCoreErrors()
```

* This method is **static**.



### Headzoo\Core\ErrorHandler::__construct
Constructor


```php
public mixed Headzoo\Core\ErrorHandler::__construct(psr\Log\LoggerInterface $logger)
```


##### Arguments

* $logger **psr\Log\LoggerInterface** - Used to log errors



### Headzoo\Core\ErrorHandler::__destruct
Destructor


```php
public mixed Headzoo\Core\ErrorHandler::__destruct()
```




### Headzoo\Core\ErrorHandler::getLastError
Returns the last generated exception

Generally used by the error callbacks, this method returns an exception instance which
describes the error that was handled.
```php
public Exception|null Headzoo\Core\ErrorHandler::getLastError()
```




### Headzoo\Core\ErrorHandler::getRunningEnvironment
Returns the current running environment


```php
public string Headzoo\Core\ErrorHandler::getRunningEnvironment()
```




### Headzoo\Core\ErrorHandler::setRunningEnvironment
Sets the current runtime environment

Automatically sets the default error callback for this environment if none has been
set already. Returns the previously set running environment.
```php
public string Headzoo\Core\ErrorHandler::setRunningEnvironment(string $running_env)
```


##### Arguments

* $running_env **string** - Name of the environment



### Headzoo\Core\ErrorHandler::handle
Starts handling errors

Returns true when errors are now being handled, or false when errors were already
being handled. Possibly because ::handle() had already been called, or an error has
already been handled.
```php
public bool Headzoo\Core\ErrorHandler::handle(string $running_env)
```


##### Arguments

* $running_env **string** - The running environment



### Headzoo\Core\ErrorHandler::unhandle
Stop handling errors

Restores the original uncaught exception handler, core error handler, and stops
handling errors.

Returns true when the original error handling state has been restored, or false when
the class was not handling errors. Possibly because ::handle() was never called, or
::unhandle() was already called, or an error has already been handled.
```php
public bool Headzoo\Core\ErrorHandler::unhandle()
```




### Headzoo\Core\ErrorHandler::isHandling
Returns whether errors are being handled


```php
public bool Headzoo\Core\ErrorHandler::isHandling()
```




### Headzoo\Core\ErrorHandler::setCallback
Sets the callback that will be called when an error is handled

Uses the currently running environment when none is given.

Examples:
```php
$handler->setCallback(function($handler) {
     // The $handler parameter is the ErrorHandler instance.
     // The $handler->getLastError() method returns an exception which
     // describes the error.
     include("templates/error.php");
});

$handler->setCallback("live", function($handler) {
     include("templates/error_live.php");
});

$handler->setCallback("dev", function($handler) {
     include("templates/error_dev.php");
});
```
```php
public Headzoo\Core\ErrorHandler Headzoo\Core\ErrorHandler::setCallback(string|callable $env, callable|null $callable)
```


##### Arguments

* $env **string|callable** - Name of the environment
* $callable **callable|null** - The error callback



### Headzoo\Core\ErrorHandler::getCallback
Returns the callback that will be called when an error is handled

Uses the currently running environment when none is given.
```php
public null Headzoo\Core\ErrorHandler::getCallback(string|null $env)
```


##### Arguments

* $env **string|null** - Name of the environment



### Headzoo\Core\ErrorHandler::getDefaultCallback
Returns the default callback for errors

The class has a default callback which handles errors by displaying an HTML5 page
with the error message and backtrace. This method returns a callable instace
of that default callback.
```php
public Closure Headzoo\Core\ErrorHandler::getDefaultCallback()
```




### Headzoo\Core\ErrorHandler::defaultCallback
The default error callback

This is the default error callback. It displays a web page with error information.
This should not be used in production. Ever.
```php
public mixed Headzoo\Core\ErrorHandler::defaultCallback(Headzoo\Core\ErrorHandler $handler)
```


##### Arguments

* $handler **[Headzoo\Core\ErrorHandler](Headzoo-Core-ErrorHandler.md)** - The object that handled the error



### Headzoo\Core\ErrorHandler::setCoreErrors
Sets the core errors which will be handled

By default only a few fatal errors are handled, but you can specify exactly which
errors to handle with this method.

Uses the currently running environment when none is given.

Examples:
```php
$handler->setCoreErrors([E_ERROR, E_WARNING, E_DEPRECIATED]);

$handler->setCoreErrors("live", [E_ERROR, E_WARNING]);

$handler->setCoreError("dev", [E_ERROR, E_WARNING, E_NOTICE]);
```
```php
public Headzoo\Core\ErrorHandler Headzoo\Core\ErrorHandler::setCoreErrors(string|int[] $env, int[] $errors)
```


##### Arguments

* $env **string|int[]** - Name of the environment
* $errors **int[]** - The errors to handle



### Headzoo\Core\ErrorHandler::getCoreErrors
Returns the core errors which are being handled

Uses the currently running environment when none is given.
```php
public int[] Headzoo\Core\ErrorHandler::getCoreErrors(string|null $env)
```


##### Arguments

* $env **string|null** - Name of the environment



### Headzoo\Core\ErrorHandler::removeCoreError
Stops handling a core error

Uses the currently running environment when none is given. Passing E_ALL removes all error handling
for the given environment.

Examples:
```php
$handler->removeCoreError(E_NOTICE);

$handler->removeCoreError("live", E_NOTICE);

$handler->removeCoreError("dev", E_DEPRECIATED);
```
```php
public bool Headzoo\Core\ErrorHandler::removeCoreError(string|int $env, string|int $error)
```


##### Arguments

* $env **string|int** - Name of the environment
* $error **string|int** - The error to remove



### Headzoo\Core\ErrorHandler::setUncaughtExceptions
Sets the uncaught exceptions which will be handled

By default every uncaught exception is handled, but specific types may be
specified using this method.

Uses the currently running environment when none is given.

Examples:
```php
$handler->setUncaughtExceptions([RuntimeException::class, LogicException::class]);

$handler->setUncaughtException("live", [RuntimeException::class, LogicException::class]);

$handler->setUncaughtException("dev", [InvalidArgumentException::class, LogicException::class]);
```
```php
public Headzoo\Core\ErrorHandler Headzoo\Core\ErrorHandler::setUncaughtExceptions(string|\Exception[] $env, string[] $exceptions)
```


##### Arguments

* $env **string|Exception[]** - Name of the environment
* $exceptions **string[]** - The exceptions to handle



### Headzoo\Core\ErrorHandler::getUncaughtExceptions
Returns the types of uncaught exceptions which are being handled

Uses the currently running environment when none is given.
```php
public string[] Headzoo\Core\ErrorHandler::getUncaughtExceptions(string|null $env)
```


##### Arguments

* $env **string|null** - Name of the environment



### Headzoo\Core\ErrorHandler::removeUncaughtException
Stops handling an uncaught exception

Uses the currently running environment when none is given.

Examples:
```php
$handler->removeUncaughtException(LogicError::class);

$handler->removeUncaughtException("live", LogicError::class);

$handler->removeUncaughtException("dev", InvalidArgumentException::class);
```
```php
public bool Headzoo\Core\ErrorHandler::removeUncaughtException(string|int $env, Exception|null $exception)
```


##### Arguments

* $env **string|int** - Name of the environment
* $exception **Exception|null**



### Headzoo\Core\ErrorHandler::isHandlingCoreError
Returns whether errors of the given type are being handled

Uses the currently running environment when none is given.

Examples:
```php
$is_handled = $handler->isHandlingCoreError(E_WARNING);

$is_handled = $handler->isHandlingCoreError("live", E_WARNING);
```
```php
public bool Headzoo\Core\ErrorHandler::isHandlingCoreError(string|int $env, string|int $error)
```


##### Arguments

* $env **string|int** - The environment to check
* $error **string|int** - The core error to check



### Headzoo\Core\ErrorHandler::isHandlingUncaughtException
Returns whether the given exception is being handled

Uses the currently running environment when none is given.

Examples:
```php
$is_handled = $handler->isHandlingUncaughtException(LogicException::class);

$is_handled = $handler->isHandlingUncaughtException("live", LogicException::class);
```
```php
public bool Headzoo\Core\ErrorHandler::isHandlingUncaughtException(string|int $env, string|\Exception|null $exception)
```


##### Arguments

* $env **string|int** - The environment to check
* $exception **string|Exception|null** - The exception to check



### Headzoo\Core\ErrorHandler::handleCoreError
Handles core errors

The callback used by ErrorHandler when it captures a core PHP error. This method
will call the error callback if the type of error matches one of the errors
being handled.
```php
public bool Headzoo\Core\ErrorHandler::handleCoreError(int $type, string $message, string $file, int $line)
```


##### Arguments

* $type **int** - The level of the error raised
* $message **string** - The error message
* $file **string** - The filename that the error was raised in
* $line **int** - The line number the error was raised at



### Headzoo\Core\ErrorHandler::handleUncaughtException
Handles uncaught exceptions

The callback used by ErrorHandler when it captures an unhandled exception. This
method will call the error callback if the type of exception matches the exceptions
being handled.
```php
public bool Headzoo\Core\ErrorHandler::handleUncaughtException(Exception $exception)
```


##### Arguments

* $exception **Exception** - The exception to handle



### Headzoo\Core\ErrorHandler::getCoreErrorHandler
Returns the callback being used to handle core errors


```php
public callable Headzoo\Core\ErrorHandler::getCoreErrorHandler()
```




### Headzoo\Core\ErrorHandler::getUncaughtExceptionHandler
Returns the callback being used to handle uncaught exceptions.


```php
public callable Headzoo\Core\ErrorHandler::getUncaughtExceptionHandler()
```




### Headzoo\Core\ErrorHandler::triggerError
Calls the set error callback

Called by ::handleCoreError() and ::handleUncaughtException() when the type of error
captured matches an error being handled. This method calls the error callback, and
effectively shuts down the handler.
```php
protected bool Headzoo\Core\ErrorHandler::triggerError(Exception $exception, string $label)
```


##### Arguments

* $exception **Exception** - The error
* $label **string** - Label for the reason the error is being triggered



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\ErrorHandler::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\ErrorHandler::getNamespaceName()
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
protected mixed Headzoo\Core\ErrorHandler::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\ErrorHandler::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


