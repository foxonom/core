Headzoo Core v3.0
=================
A collection of use PHP utility classes and functions.

Requirements
------------
* PHP 5.5 or greater.

Installing
----------
The library may be installed using either git or composer.

##### Git
Simply clone the project with the following command.

```
git clone git@github.com:headzoo/core.git
```

##### Composer
Add the project to your composer.json as a dependency.

```
"require": {
    "headzoo/core" : "dev-master"
}
```

Change Log
----------
##### v0.3 - 2014/03/26
* Renamed the namespace `Headzoo\Utilities` to `Headzoo\Core`.
* Renamed the project `headzoo/core`.
* Created core class `Obj`.
* Added the trait `ConstantsTrait`.
* Created the `ConstantsTrait` trait.
* Created the `AbstractEnum` class.
* The `Strings` class is now made to work seamlessly with multi-byte strings.
* Renamed `Strings::transformCamelCaseToUnderscore` to `Strings::camelCaseToUnderscore`.
* Renamed `Strings::transformUnderscoreToCamelCase` to `Strings::underscoreToCamelCase`.
* Added new methods to the `Strings` class:
    * `Strings::startsWith`.
    * `Strings::endsWith`.
    * `Strings::startsUpper`.
    * `Strings::startsLower`.
    * `Strings::replace`.
    * `Strings::length`.
    * `Strings::chars`.
    * `Strings::toUpper`.
    * `Strings::toLower`.
    * `Strings::ucFirst`.
    * `Strings::lcFirst`.
    * `Strings::title`.
    * `Strings::sub`.
    * `Strings::split`.
    * `Strings::transform`.
    
##### v0.2.3 - 2014/03/25
* Increased the minimum PHP version requirement to 5.5.0. Long live, ClassName::class!
* Added the method `Strings::quote`.
* Added the method `Arrays::conjunct`.
* Added the method `Functions::swapCallable`.
* Added the class `Validator`.

##### v0.2.2 - 2014/03/24
* Added the method `Arrays::findString`.

##### v0.2.1 - 2014/03/24
* Changed visibility of `Complete::invoke` to public.

##### v0.2 - 2014/03/24
* Added the `Complete` class.

##### v0.1 - 2014/03/23
* First version released under MIT license.

License
-------
This content is released under the MIT License. See the included LICENSE for more information.
