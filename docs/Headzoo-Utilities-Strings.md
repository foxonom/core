Headzoo\Utilities\Strings
===============

Contains static methods for working with strings.

The methods of this class are normalized, in that the string to operate on
is always the first argument.


* Class name: Strings
* Namespace: Headzoo\Utilities
* Parent class: [Headzoo\Utilities\Core](Headzoo-Utilities-Core.md)



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
private string $chars_upper = "ABCDEFGHJKMNOPQRSTUVWXYZ"
```

* This property is **static**.


### $chars_numbers
Numbers, used to generate random strings


```php
private string $chars_numbers = "023456789"
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


### Headzoo\Utilities\Strings::setUseMultiByte
Sets whether to use the "mbstring" extension for string operations

The "mbstring" extension must be loaded when set to true. An exception is thrown if the extension
is not loaded. When set to false, the mbstring extension will not be used even if the
extension is loaded.

When turned on with true, the character set used by methods of this class will be set to the return
value of ::getDefaultCharacterSet(). Use the ::setCharacterSet() method to set a different character set.
See the ::getDefaultCharacterSet() method for more information.
```php
public mixed Headzoo\Utilities\Strings::setUseMultiByte(bool $use_mbstring)
```

* This method is **static**.

##### Arguments

* $use_mbstring **bool** - Whether to use the multi-byte extension



### Headzoo\Utilities\Strings::setCharacterSet
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
public mixed Headzoo\Utilities\Strings::setCharacterSet(string $char_set)
```

* This method is **static**.

##### Arguments

* $char_set **string** - The name of the character encoding



### Headzoo\Utilities\Strings::getDefaultCharacterSet
Returns the default character set used for string operations

Returns the value of the "default_charset" php.ini directive, or ::DEFAULT_CHAR_SET when
php has not been configured to use a default character set.
```php
public string Headzoo\Utilities\Strings::getDefaultCharacterSet()
```

* This method is **static**.



### Headzoo\Utilities\Strings::quote
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
public string Headzoo\Utilities\Strings::quote(string $str, string $quote)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to quote
* $quote **string** - The quote character to use



### Headzoo\Utilities\Strings::random
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

The return value will never contain characters "i", "I", "l", "L", and
"1".
```php
public string Headzoo\Utilities\Strings::random(int $len, int $char_class)
```

* This method is **static**.

##### Arguments

* $len **int** - Length of the string to return
* $char_class **int** - Class of characters to use



### Headzoo\Utilities\Strings::camelCaseToUnderscore
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
public string Headzoo\Utilities\Strings::camelCaseToUnderscore(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Utilities\Strings::underscoreToCamelCase
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
public string Headzoo\Utilities\Strings::underscoreToCamelCase(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Utilities\Strings::startsWith
Returns whether the string starts with another string, using multi-byte functions when enabled


```php
public bool Headzoo\Utilities\Strings::startsWith(string $str, string $find)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search
* $find **string** - The string to find



### Headzoo\Utilities\Strings::endsWith
Returns whether the string ends with another string, using multi-byte functions when enabled


```php
public bool Headzoo\Utilities\Strings::endsWith(string $str, string $find)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search
* $find **string** - The string to find



### Headzoo\Utilities\Strings::startsUpper
Returns whether the string starts with an upper case character, using multi-byte functions when enabled


```php
public bool Headzoo\Utilities\Strings::startsUpper(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search



### Headzoo\Utilities\Strings::startsLower
Returns whether the string starts with a lower case character, using multi-byte functions when enabled


```php
public bool Headzoo\Utilities\Strings::startsLower(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to search



### Headzoo\Utilities\Strings::replace
Replaces characters in a string, using multi-byte functions when enabled


```php
public string Headzoo\Utilities\Strings::replace(string $str, string $search, string $replace)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform
* $search **string** - The string to find
* $replace **string** - The replacement string



### Headzoo\Utilities\Strings::length
Returns the number of characters in the string, using multi-byte functions when enabled


```php
public int Headzoo\Utilities\Strings::length(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to count



### Headzoo\Utilities\Strings::chars
Splits a string into individual characters, using multi-byte functions when enabled


```php
public array Headzoo\Utilities\Strings::chars(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to split



### Headzoo\Utilities\Strings::toUpper
Transforms a string to lower case, using multi-byte functions when enabled


```php
public string Headzoo\Utilities\Strings::toUpper(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Utilities\Strings::toLower
Transforms a string to upper case, using multi-byte functions when enabled


```php
public string Headzoo\Utilities\Strings::toLower(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Utilities\Strings::ucFirst
Transforms the first letter of each word to upper case, using multi-byte functions when enabled


```php
public string Headzoo\Utilities\Strings::ucFirst(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Utilities\Strings::lcFirst
Transforms the first letter of each word to lower case, using multi-byte functions when enabled


```php
public string Headzoo\Utilities\Strings::lcFirst(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Utilities\Strings::title
Upper cases the first letter of each word in the string, using multi-byte functions when enabled


```php
public string Headzoo\Utilities\Strings::title(string $str)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform



### Headzoo\Utilities\Strings::sub
Returns a portion of the string, using multi-byte functions when enabled


```php
public string Headzoo\Utilities\Strings::sub(string $str, int $start, int $end)
```

* This method is **static**.

##### Arguments

* $str **string** - The string
* $start **int** - The start position
* $end **int** - The end position



### Headzoo\Utilities\Strings::split
Splits a string by regular expression, using multi-byte functions when enabled


```php
public array Headzoo\Utilities\Strings::split(string $str, string $pattern, string $limit)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to split
* $pattern **string** - The regular expression pattern
* $limit **string** - If optional parameter limit is specified, it will be split in limit elements as maximum



### Headzoo\Utilities\Strings::transform
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
public mixed Headzoo\Utilities\Strings::transform(string $str, int $transformation)
```

* This method is **static**.

##### Arguments

* $str **string** - The string to transform
* $transformation **int** - The transformation to apply



### Headzoo\Utilities\Core::className
Returns the name of the class


```php
public string Headzoo\Utilities\Strings::className()
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
protected mixed Headzoo\Utilities\Strings::throwException(string $exception, string $message, int $code)
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
private string Headzoo\Utilities\Strings::interpolate(string $message, array $context)
```

* This method is **static**.

##### Arguments

* $message **string** - Message with placeholders
* $context **array** - Values to replace in the message


