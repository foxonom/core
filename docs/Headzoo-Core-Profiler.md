Headzoo\Core\Profiler
===============

A simple profiling class.

Examples:
```php
// Basic usage.
$profiler = new Profiler();
$profiler->start();

// ... some operation you want to profile ...

$profiler->stop();

// Outputs:
// "Profile time for 'default': 0.00030207633972168"

// Getting the profile information instead of displaying it.
$profiler = new Profiler();
$profiler->start();

// ... some operation you want to profile ...

$microtime = $profile->stop(false);
var_dump($microtime);

// double(0.00030207633972168)

// Giving an ID to the profile info.
$profiler = new Profiler();
$profiler->start("profile1");

// ... some operation you want to profile ...

$profiler->stop("profile1");

// Outputs:
// "Profile time for 'profile1': 0.00030207633972168"

// Nested profiling.
$profiler = new Profiler();
$profiler->start("profile1");

// ... first operation you want to profile ...

$profile->start("profile2");

// ... second operation you want to profile ...

$profiler->stop("profile2");
$profiler->stop("profile1");

// Outputs:
// "Profile time for 'profile2': 0.00030207633972168"
// "Profile time for 'profile1': 0.000202397223"

// Using the factory method.
$profiler = Profiler::factory();
$profiler->start();

// ... some operation you want to profile ...

$profiler->stop();

// Outputs:
// "Profile time for 'default': 0.00030207633972168"

// Have the profiler started from the factory method.
$profiler = Profiler::factory(true);

// ... some operation you want to profile ...

$profiler->stop();

// Have the profiler started with an ID from the factory method.
$profiler = Profiler::factory("profile1");

// .. some operation you want to profile ...

$profiler->stop();

// Profiling can be globally enabled and disabled.
Profiler::enabled(false);

// You can also log the profile information with an instance of psr/Log/LoggerInterface.
$profiler = new Profiler(new FileLogger());
```


* Class name: Profiler
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)



Constants
----------


### DEFAULT_ENABLED
The default enabled value


```php
const DEFAULT_ENABLED = true
```





### DEFAULT_ID
The default profiling id


```php
const DEFAULT_ID = "default"
```





Properties
----------


### $enabled
Whether profiling is enabled


```php
protected bool $enabled = self::DEFAULT_ENABLED
```

* This property is **static**.


### $start
Array of microtimes


```php
protected array $start = array()
```



### $logger
Logs profile times


```php
protected psr\Log\LoggerInterface $logger
```



### $log_level
The logging level


```php
protected string $log_level
```



Methods
-------


### Headzoo\Core\Profiler::enabled
Gets or sets whether profiling is globally enabled

Globally enables or disables profiling when $enabled is set to a boolean value. Returns whether profiling
is enabled when called without any argument.
```php
public bool Headzoo\Core\Profiler::enabled(null|bool $enabled)
```

* This method is **static**.

##### Arguments

* $enabled **null|bool** - Enables or disables profiling



### Headzoo\Core\Profiler::factory
Factory method

Creates an returns a new Profiler instance. The returned profiler will have already started
profiling when $id is non-false value. When $id is set to boolean true, the profiling
will be started with the default ID. Otherwise the value of $id will be used as the ID.

Examples:
```php
$profiler = Profiler::factory();
var_dump($profiler->isStarted());
// Outputs: bool(false)

$profiler = Profiler::factory(true);
var_dump($profiler->isStarted());
// Outputs: bool(true)

$profiler = Profiler::factory("profile1");
var_dump($profiler->isStarted("profile1"));
// Outputs: bool(true)
```
```php
public Headzoo\Core\Profiler Headzoo\Core\Profiler::factory(null|string|bool $id)
```

* This method is **static**.

##### Arguments

* $id **null|string|bool** - Start profiling, or start profiling with this id



### Headzoo\Core\Profiler::run
Profiles one or more calls to a function

Calls a function the number of times specified by $num_runs, and displays the total run time,
the average run time, and the time for each run. Also returns an array with the same
information.

Regardless of the number of runs, the output only shows 40 run times: the first 20 and the
last 20. The returned array always contains the times for each run.

Note: Profiling must be enabled to use this method.

Examples:
```php
// This example runs the closure 100 times, and displays the profile results.
$runs = Profiler::run(100, true, function() {
     ... do something here ...
});

// Output:
//
// Total Runs:                 100
// Total Time:      0.099596977234
// Average Time:    0.000981624126
// -------------------------------
// Run #1           0.000479936599
// Run #2           0.000968933105
// Run #3           0.000982999801
// Run #4           0.000988006591
// ......
// Run #97          0.000985145568
// Run #98          0.000983953476
// Run #99          0.000997066497
// Run #100         0.000993013382

print_r($runs);
// Output:
// [
//       "total"   => 0.099596977234,
//       "average" => 0.000981624126,
//       "runs"    => [
//           0.0004799365997,
//           0.0009689331055,
//           ....
//           0.0009930133820
//       ]
// ]

// Use false for the second argument to turn off displaying the results.
$runs = Profiler::run(100, false, function() {
     ... do something here ...
});

// The middle argument may be omitted, and defaults to true.
$runs = Profiler::run(100, function() {
     ... do something here ...
});
```
```php
public array Headzoo\Core\Profiler::run(int $num_runs, bool $display, callable $callable)
```

* This method is **static**.

##### Arguments

* $num_runs **int** - The number of times to run the function
* $display **bool** - Whether or not to display the results
* $callable **callable** - The function to call



### Headzoo\Core\Profiler::__construct
Constructor


```php
public mixed Headzoo\Core\Profiler::__construct(psr\Log\LoggerInterface $logger, string $log_level)
```


##### Arguments

* $logger **psr\Log\LoggerInterface** - Used to log profiling information
* $log_level **string** - The logging level



### Headzoo\Core\Profiler::setLogger
Sets the logger instance


```php
public Headzoo\Core\Profiler Headzoo\Core\Profiler::setLogger(psr\Log\LoggerInterface $logger, string $log_level)
```


##### Arguments

* $logger **psr\Log\LoggerInterface** - Used to log profiling information
* $log_level **string** - The logging level



### Headzoo\Core\Profiler::reset
Resets the profiling state

Stops any profiling which may have been started.

This method always returns true.

Examples:
```php
$profiler = new Profiler();
$profiler->start();
var_dump($profiler->isStarted();
$profiler->reset();
var_dump($profiler->isStarted());

// Outputs:
// bool(true)
// bool(false)
```
```php
public bool Headzoo\Core\Profiler::reset()
```




### Headzoo\Core\Profiler::start
Start profiling

The default profiling ID will be used when none is given.
```php
public int Headzoo\Core\Profiler::start(string $id)
```


##### Arguments

* $id **string** - Identifies this profiling operation



### Headzoo\Core\Profiler::stop
Stop profiling

Stops profiling for the given $id, and returns and/or displays the number of microseconds which
have elapsed since the call to ::start() with the same $id. The default profiling ID will be used
when none is given.

Displays the profiling time when $display is set to true. A boolean value may be passed as the first
argument to this method to control displaying output, and the default $id value will be used.
The time elapsed is still returned.

Examples:
```php
$profiler = new Profiler();
$profiler->start();
usleep(100);
$micro = $profiler->stop();
var_dump($micro);

// Outputs:
// "Profile time for 'default': 0.00030207633972168"
// double(0.00030207633972168)

$profiler->start();
usleep(100);
$micro = $profiler->stop(false);
var_dump($micro);

// Outputs:
// double(0.00030207633972168)

$profiler->start("profile1");
usleep(100);
$micro = $profiler->stop("profile1");
var_dump($micro);

// Outputs:
// "Profile time for 'profile1': 0.00030207633972168"
// double(0.00030207633972168)
```
```php
public float|bool Headzoo\Core\Profiler::stop(string $id, bool $display)
```


##### Arguments

* $id **string** - Identifies this profiling operation
* $display **bool** - Whether or not to display the profiling data



### Headzoo\Core\Profiler::isStarted
Returns whether profiling has been started for the given id


```php
public bool Headzoo\Core\Profiler::isStarted(string $id)
```


##### Arguments

* $id **string** - Identifies this profiling operation



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\Profiler::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\Profiler::getNamespaceName()
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
protected mixed Headzoo\Core\Profiler::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\Profiler::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


