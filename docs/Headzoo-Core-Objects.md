Headzoo\Core\Objects
===============

Contains static methods for working with objects and classes.




* Class name: Objects
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)







Methods
-------


### Headzoo\Core\Objects::getFullName
Returns a fully qualified class name

Returns a string containing the fully qualified name of the class. The name is normalized by
removing leading and trailing namespace separators.

The $obj argument may be either an object, or a string. When given a string the value *shout*
be the name of a class, but this method does not check if the class exists.

Examples:
```php
echo Objects::getFullName(new stdClass());
// Outputs: "stdClass"

echo Objects::getFullName('\Headzoo\Core\Objects');
// Outputs: "Headzoo\Core\Objects"
```
```php
public string Headzoo\Core\Objects::getFullName(object|string $obj)
```

* This method is **static**.

##### Arguments

* $obj **object|string** - An object or class name



### Headzoo\Core\Objects::isObject
Returns whether a value is an object, or array of objects

Returns a boolean value indicating whether the $obj argument is an object, or an array of nothing
but objects. Returns false when $obj is neither an object nor array.

Examples:
```php
$obj = new stdClass();
$is = Objects::isObject($obj);
var_dump($is);

// Outputs: bool(true)

$objs = [
     new stdClass(),
     new Headzoo\Core\Strings()
];
$is = Objects::isObject($objs);
var_dump($is);

// Outputs: bool(true)

$is = Objects::isObject('stdClass');
var_dump($is);

// Outputs: bool(false)

$objs = [
     new stdClass(),
     'Headzoo\Core\Strings'
];
$is = Objects::isObject($objs);
var_dump($is);

// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Objects::isObject(object|array $obj)
```

* This method is **static**.

##### Arguments

* $obj **object|array** - The object or array of objects to test



### Headzoo\Core\Objects::isInstance
Returns whether the object, or array of objects, is an instance of a class

Similar to PHP's own instanceof comparison operator, this method differs in two ways:
 - The first argument may be an array of objects to test.
 - The second argument may be a string with the name of a class.

Throws an exception when $obj is not an object or an array of objects.

Examples:
```php
$is = Objects::isInstance(new Strings(), new Strings());
var_dump($is);
// Outputs: bool(true)

$is = Objects::isInstance(new Strings(), Strings::class);
var_dump($is);
// Outputs: bool(true)

$is = Objects::isInstance(new Arrays(), new Strings());
var_dump($is);
// Outputs: bool(false)

$is = Objects::isInstance(new Arrays(), Arrays::class);
var_dump($is);
// Outputs: bool(false)

$objects = [
     new Strings(),
     new Strings()
];
$is = Objects::isInstance($objects, Strings::class);
var_dump($is);
// Outputs: bool(true)

$objects = [
     new Strings(),
     new stdClass()
];
$is = Objects::isInstance($objects, Strings::class);
var_dump($is);
// Outputs: bool(false)

$objects = [
     [
         new Strings(),
         new Strings()
     ],
     [
         new Strings(),
         new Strings()
     ]
];
$is = Objects::isInstance($objects, Strings::class);
var_dump($is);
// Outputs: bool(true)
```
```php
public bool Headzoo\Core\Objects::isInstance(object|object[] $obj, object|string $class)
```

* This method is **static**.

##### Arguments

* $obj **object|object[]** - The object or array of objects to test
* $class **object|string** - Object or string naming a class



### Headzoo\Core\Objects::equals
Returns whether two objects are equal to each other

For two objects to be equal they must be of the same class type, and public properties from each
must have the same value. When $deep is true, the protected and private property values must
also be equal. A strict === comparison is done between property values.

Examples:
```php
$obj_a = new Strings();
$obj_b = new Strings();
$obj_a->value = "monkey";
$obj_b->value = "moneky";
$is_equal = Objects::equals($obj_a, $obj_b);
var_dump($is_equal);

// Outputs: bool(true)

$obj_a = new Strings();
$obj_b = new Strings();
$obj_a->value = "monkey";
$obj_b->value = "donkey";
$is_equal = Objects::equals($obj_a, $obj_b);
var_dump($is_equal);

// Outputs: bool(false)
```
```php
public bool Headzoo\Core\Objects::equals(object $obj_a, object $obj_b, bool $deep)
```

* This method is **static**.

##### Arguments

* $obj_a **object** - The first object to test
* $obj_b **object** - The second object to test
* $deep **bool** - True to check both public and private properties



### Headzoo\Core\Objects::merge
Merges two or more objects

Copies the public property values from $obj2 to $obj1, and returns $obj1. Properties from $obj2
are either added to $obj1, or overwrite existing properties with the same name. Null values
do not overwrite non-null values. More than two objects may be merged.

Protected and private properties are not merged.

Examples:
```php
$obj_a = new stdClass();
$obj_a->job = "Circus Freak";

$obj_b = new stdClass();
$obj_b->name = "Joe"
$obj_b->job  = "Space Buccaneer";

$obj_c = new stdClass();
$obj_c->age = 23;

$merged = Objects::merge($obj_a, $obj_b, $obj_c);
var_dump($obj_a === $merged);
var_dump($merged);

// Outputs:
// bool(true);
// class stdClass {
//   public $name => string(4) "Joe"
//   public $job  => string(15) "Space Buccaneer"
//   public $age  => int(23)
//
// }

// The objects do not have to be the same type.
$obj_a = new Strings();
$obj_a->value = "PhpStorm Rocks!";

$obj_b = new Strings();
$obj_b->value = "So does IntelliJ IDEA!";

$obj_c = new stdClass();
$obj_c->website = "http://www.jetbrains.com/";

$merged = Objects::merge($obj_a, $obj_b, $obj_c);
var_dump($merged === $obj_a);
var_dump($merged);

// Outputs:
// bool(true)
// class Strings {
//   public $value   => string(22) "So does IntelliJ IDEA!",
//   public $website => string(25) "http://www.jetbrains.com/"
// }

// Easily merge objects into a new "blank" object.
$obj_a = new Strings();
$obj_a->value = "PhpStorm Rocks!";

$obj_b = new Arrays();
$obj_b->count = 23;

$merged = Objects::merge(new stdClass(), $obj_a, $obj_b);
var_dump($merged);

// Outputs:
// class stdClass {
//   public $value => string(15) "PhpStorm Rocks!",
//   public $count => 23
// }
```
```php
public mixed Headzoo\Core\Objects::merge(object $obj, object $obj2)
```

* This method is **static**.

##### Arguments

* $obj **object** - The base object
* $obj2 **object** - Gets merged into the base object



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\Objects::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\Objects::getNamespaceName()
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
protected mixed Headzoo\Core\Objects::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\Objects::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


