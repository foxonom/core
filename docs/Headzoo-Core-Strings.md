Headzoo\Core\Strings
===============

Contains static methods for working with strings.

The methods of this class are normalized, in that the string to operate on
is always the first argument.


* Class name: Strings
* Namespace: Headzoo\Core
* Parent class: [Headzoo\Core\Obj](Headzoo-Core-Obj.md)



Constants
----------


### DEFAULT_CHAR_SET
Default character set when not defined in the php.ini


```php
const DEFAULT_CHAR_SET = "ISO-8859-1"
```





### CHARS_LOWER
Use lower case letters in random strings


```php
const CHARS_LOWER = 1
```





### CHARS_UPPER
Use upper case letters in random strings


```php
const CHARS_UPPER = 2
```





### CHARS_NUMBERS
Use numbers in random strings


```php
const CHARS_NUMBERS = 4
```





### CHARS_PUNCTUATION
Use punctuation in random strings


```php
const CHARS_PUNCTUATION = 8
```





### TR_UPPER
Transform to upper case


```php
const TR_UPPER = 1
```





### TR_LOWER
Transform to lower case


```php
const TR_LOWER = 2
```





### TR_TITLE
Transform the first character of each word in a string, and the rest to lower case


```php
const TR_TITLE = 3
```





### TR_UC_FIRST
Transform the first character in a sentence into uppercase


```php
const TR_UC_FIRST = 4
```





### TR_LC_FIRST
Transform the first character in a sentence to lower case


```php
const TR_LC_FIRST = 5
```





### TR_UNDERSCORE
Transform a string from CamelCaseText to underscore_text


```php
const TR_UNDERSCORE = 6
```





### TR_CAMEL_CASE
Transform a string from underscore_text to CamelCaseText


```php
const TR_CAMEL_CASE = 7
```





### TRUNC_START
Truncate a string at the start


```php
const TRUNC_START = 1
```





### TRUNC_MIDDLE
Truncate a string in the middle


```php
const TRUNC_MIDDLE = 2
```





### TRUNC_END
Truncate a string at the end


```php
const TRUNC_END = 3
```





Properties
----------


### $char_set
The character set to use


```php
private string $char_set = null
```

* This property is **static**.


### $use_mbstring
Whether to use the mbstring extension


```php
private bool $use_mbstring = false
```

* This property is **static**.


### $chars_lower
Lower case letters, used to generate random strings


```php
private string $chars_lower = "abcdefghjkmnopqrstuvwxyz"
```

* This property is **static**.


### $chars_upper
Upper case letters, used to generate random strings


```php
private string $chars_upper = "ABCDEFGHJKMNPQRSTUVWXYZ"
```

* This property is **static**.


### $chars_numbers
Numbers, used to generate random strings


```php
private string $chars_numbers = "23456789"
```

* This property is **static**.


### $chars_punctuation
Punctuation, used to generate random strings


```php
private string $chars_punctuation = "!@#$%^&*()+=-_,.?{}[]<>"
```

* This property is **static**.


### $__mbstring_extension_name
Name of the mbstring extension

Used for testing purposes. The value should not be changed unless testing.
```php
public string $__mbstring_extension_name = "mbstring"
```

* This property is **static**.


Methods
-------


### Headzoo\Core\Strings::setUseMultiByte
Sets whether to use the "mbstring" extension for string operations

The "mbstring" extension must be loaded when set to true. An exception is thrown if the extension
is not loaded. When set to false, the mbstring extension will not be used even if the
extension is loaded.

When turned on with true, the character set used by methods of this class will be set to the return
value of ::getDefaultCharacterSet(). Use the ::setCharacterSet() method to set a different character set.
See the ::getDefaultCharacterSet() method for more information.
```php
public mixed Headzoo\Core\Strings::setUseMultiByte(bool $use_mbstring)
```

* This method is **static**.

##### Arguments

* $use_mbstring **bool** - Whether to use the multi-byte extension



### Headzoo\Core\Strings::setCharacterSet
Sets the character set this class uses for string operations

The value of $char_set must be a valid character set name as defined by mb_list_encodings(), but the
name may use any case and omit dashes. For example these are all valid character set
names: "UTF-8", "utf-8", "UTF8", "utf8". The name of the encoding will be converted to it's proper
name.

Note: The return value from ::getDefaultCharSet() will be used when $char_set is empty. See that method
for more information.

Calling this method with any value other than "ASCII", "ISO-8859-1", empty, or the return value
from ::getDefaultCharacterSet() will automatically call ::setUseMultiByte(true) to turn on the use of
the "mbstring" extension. See the documentation on ::setUseMultiByte() for more information.

Note: This method does not change php's default internal encoding, eg by calling mb_internal_encoding().
It only affects the encoding used when calling methods of this class.
```php
public mixed Headzoo\Core\Strings::setCharacterSet(string $char_set)
```

* This method is **static**.

##### Arguments

* $char_set **string** - The name of the character encoding



### Headzoo\Core\Strings::getDefaultCharacterSet
Returns the default character set used for string operations

Returns the value of the "default_charset" php.ini directive, or ::DEFAULT_CHAR_SET when
php has not been configured to use a default character set.
```php
public string Headzoo\Core\Strings::getDefaultCharacterSet()
```

* This method is **static**.



### Headzoo\Core\Strings::quote
Simply wraps a string in quote characters

Useful as a callback function to functions like array_map(), this method wraps a string in your choose of
quote character.

Note: Quote characters found in the string are not escaped with slashes.

Example:
```php
$str = "Mary had a little lamb.";
echo Strings::quote($str);
// Outputs: 'Mary had a little lamb.'

echo Strings::quote($str, "`");
// Outputs: `Mary had a little lamb.`
```
```php
public string Headzoo\Core\Strings::quote(string $str, string $quote)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to quote
* $quote **string** - The quote character to use



### Headzoo\Core\Strings::random
Returns a completely random string $len characters long

The $char_class argument controls which class of characters will be used
in the random string: lower case letters, upper case letters, numbers,
and punctuation. Use the self::CHARS constants and bitwise OR to
specify several character classes.

Example:
```php
Strings::random(10, Strings::CHARS_LOWER | Strings::CHARS_UPPER);
```

The return value will container lower case and upper case letters. By
default the character class used is
(Strings::CHARS_LOWER | Strings::CHARS_UPPER | Strings::CHARS_NUMBERS),
which is every character except punctuation.

The return value will never contain characters "i", "I", "l", "L", "O", 0 and
"1".
```php
public string Headzoo\Core\Strings::random(int $len, int $char_class)
```

* This method is **static**.

##### Arguments

* $len **int** - Length of the string to return
* $char_class **int** - Class of characters to use



### Headzoo\Core\Strings::camelCaseToUnderscore
Transforms a string with CamelCaseText into a string with underscore_text

Note: The method also treats dashes "-" as underscores.

Example:
```php
echo Strings::camelCaseToUnderscore("CamelCaseString");
// Outputs: "camel_case_string"

echo Strings::camelCaseToUnderscore("MaryHadALittleLamb");
// Outputs: "mary_had_a_little_lamb"
```
```php
public string Headzoo\Core\Strings::camelCaseToUnderscore(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Core\Strings::underscoreToCamelCase
Transforms a string with underscore_text into a string with CamelCaseText

Note: The method also treats dashes "-" as underscores.

Example:
```php
echo Strings::underscoreToCamelCase("camel_case_string");
// Outputs: "CamelCaseString"

$str = Strings::underscoreToCamelCase("camel-case-string");
// Outputs: "CamelCaseString"
```
```php
public string Headzoo\Core\Strings::underscoreToCamelCase(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Core\Strings::startsWith
Returns whether the string starts with another string

Returns a boolean value indicating whether a string starts with another string. The comparison is
case-sensitive when $match_case is true, and case-insensitive when false.

Examples:
```php
$is = Strings::startsWith("How I wish, how I wish you were here.", "How");
var_dump($is);

// Outputs: bool(true)

$is = Strings::startsWith("We're just two lost souls, Swimming in a fish bowl", "we're");
var_dump($is);

// Outputs: bool(false)

$is = Strings::startsWith("We're just two lost souls, Swimming in a fish bowl", "we're", false);
var_dump($is);

// Outputs: bool(true)
```
```php
public bool Headzoo\Core\Strings::startsWith(string $str, string $find, bool $match_case)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search
* $find **string** - The string to find
* $match_case **bool** - True to match the exact case, false for case-insensitive match



### Headzoo\Core\Strings::endsWith
Returns whether the string ends with another string

Returns a boolean value indicating whether a string ends with another string. The comparison is
case-sensitive when $match_case is true, and case-insensitive when false.

Examples:
```php
$is = Strings::endsWith("Running over the same old ground.", "ground.");
var_dump($is);

// Outputs: bool(true)

$is = Strings::endsWith("What have we found? The same old fears.", "Fears.");
var_dump($is);

// Outputs: bool(false)

$is = Strings::endsWith("What have we found? The same old fears.", "Fears.", false);
var_dump($is);

// Outputs: bool(true)
```
```php
public bool Headzoo\Core\Strings::endsWith(string $str, string $find, bool $match_case)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search
* $find **string** - The string to find
* $match_case **bool** - True to match the exact case, false for case-insensitive match



### Headzoo\Core\Strings::startsUpper
Returns whether the string starts with an upper case character

Returns a boolean value indicating whether the first character in a string is upper
case.

Examples:
```php
$is = Strings::startsUpper("Welcome my son, welcome to the machine.");
var_dump($is);

// Output: bool(true);

$is = Strings::startsUpper("you've been in the pipeline, filling in time");
var_dump($is);

// Output: bool(false)
```
```php
public bool Headzoo\Core\Strings::startsUpper(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search



### Headzoo\Core\Strings::startsLower
Returns whether the string starts with a lower case character

Returns a boolean value indicating whether the first character in a string is lower
case.

Examples:
```php
$is = Strings::startsLower("And sail on the steel breeze.");
var_dump($is);

// Output: bool(false);

$is = Strings::startsLower("come on you miner for truth and delusion, and shine!");
var_dump($is);

// Output: bool(true)
```
```php
public bool Headzoo\Core\Strings::startsLower(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search



### Headzoo\Core\Strings::replace
Replaces characters in a string

Example:
```php
$str = "With flowers and my love both never to come back";
$search = "flowers";
$replace = "roses";
echo Strings::replace($str, $search, $replace);

// Outputs: "With roses and my love both never to come back"
```
```php
public string Headzoo\Core\Strings::replace(string $str, string $search, string $replace)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform
* $search **string** - The string to find
* $replace **string** - The replacement string



### Headzoo\Core\Strings::length
Returns the number of characters in the string

Note: When multi-byte support is enabled, the method returns the true number of characters
instead of the number of bytes.

Example:
```php
echo Strings::length("You can't say we're satisfied");

// Outputs: 29
```
```php
public int Headzoo\Core\Strings::length(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to count



### Headzoo\Core\Strings::chars
Splits a string into individual characters

Returns an array with the individual characters from a string.

Example:
```php
$chars = Strings::chars("Yeah!");
print_r($chars);

// Outputs:
// [
//       "Y",
//       "e",
//       "a",
//       "h",
//       "!"
// ]
```
```php
public array Headzoo\Core\Strings::chars(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to split



### Headzoo\Core\Strings::toUpper
Transforms a string to upper case

Example:
```php
echo Strings::toUpper("With no loving in our souls and no money in our coats");

// Outputs: "WITH NO LOVING IN OUR SOULS AND NO MONEY IN OUR COATS"
```
```php
public string Headzoo\Core\Strings::toUpper(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Core\Strings::toLower
Transforms a string to lower case

Example:
```php
echo Strings::toLower("But Angie, Angie, you can't say we never tried");

// Outputs: "but angie, angie, you can't say we never tried"
```
```php
public string Headzoo\Core\Strings::toLower(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Core\Strings::ucFirst
Transforms the first letter of a string to upper case

Example:
```php
echo Strings::ucFirst("people say that I've found a way");

// Outputs: "People say that I've found a way"
```
```php
public string Headzoo\Core\Strings::ucFirst(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Core\Strings::lcFirst
Transforms the first letter of a string to lower case

Example:
```php
echo Strings::lcFirst("To make you say that you love me");

// Outputs: "to make you say that you love me"
```
```php
public string Headzoo\Core\Strings::lcFirst(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Core\Strings::title
Upper cases the first letter of each word in the string

Example:
```php
echo Strings::title("Hey hey hey hey, and I'm cryin' tears all through the years");

// Outputs: "Hey Hey Hey Hey, And I'm Cryin' Tears All Through The Years"
```
```php
public string Headzoo\Core\Strings::title(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Core\Strings::sub
Returns a portion of the string

See the documentation for substr for details of each argument.

Example:
```php
echo Strings::sub("Make you do right, love", 19, 4);

// Outputs: "love"
```
```php
public string Headzoo\Core\Strings::sub(string $str, int $start, int $end)
```

* This method is **static**.

##### Arguments

* $str **string** - The string
* $start **int** - The start position
* $end **int** - The end position



### Headzoo\Core\Strings::truncate
Truncates strings which exceed a maximum length

An ellipsis is added to the string to indicate it's been shortened. The final length
of the string, including ellipsis, will be the length specified by $max_len.

Strings may be truncated in three places: the start, middle, and end.

Examples:
```php
// Truncating a string at the end.
echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_END);

// Outputs: "Mary had a little..."

// Truncating a string at the start.
echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_START);

// Outputs: "...as white as snow."

// Truncating a string in the middle.
echo Strings::truncate("Mary had a little lamb, whose fleece was white as snow.", 20, Strings::TRUNC_MIDDLE);

// Outputs: "Mary ha...e as snow."
```
```php
public string Headzoo\Core\Strings::truncate(string $str, int $max_len, int $pos, string $ellipsis)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to truncate
* $max_len **int** - The maximum length
* $pos **int** - Where in the string the cut should be made
* $ellipsis **string** - A string which indicates the string was truncated



### Headzoo\Core\Strings::transform
Transform a string

This method wraps the functions ::toLower(), ::toUpper(), ::title(), ::ucFirst(), ::lcFirst(),
::camelCaseToUnderscore(), and ::underscoreToCamelCase()
.
The notable difference is strings are passed by reference to this method.

The value of $transformation must be one of the Strings::TR constants, which map to the following methods:
 ::TR_LOWER      - ::toLower().
 ::TR_UPPER      - ::toUpper().
 ::TR_TITLE      - ::title().
 ::TR_UC_FIRST   - ::ucFirst().
 ::TR_LC_FIRST   - ::lcFirst().
 ::TR_UNDERSCORE - ::camelCaseToUnderscore()
 ::TR_CAMEL_CASE - ::underscoreToCamelCase()
```php
public mixed Headzoo\Core\Strings::transform(string $str, int $transformation)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform
* $transformation **int** - The transformation to apply



### Headzoo\Core\Obj::getClassName
Returns the name of the class


```php
public string Headzoo\Core\Strings::getClassName()
```




### Headzoo\Core\Obj::getNamespaceName
Returns the name of the class namespace

The namespace will not have a leading forward-slash, eg "Headzoo\Core" instead
of "\Headzoo\Core". An empty string is returned when the class is in the
global namespace.
```php
public string Headzoo\Core\Strings::getNamespaceName()
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
```

The built in place holders:
 {me}        - The name of the class throwing the exception
 {exception} - The name of the exception being thrown
 {code}      - The exception code
 {date}      - The date the exception was thrown
```php
protected mixed Headzoo\Core\Strings::toss(string $exception, string $message, int $code)
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
private string Headzoo\Core\Strings::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


