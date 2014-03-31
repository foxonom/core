Headzoo\Core\Conversions
===============

Utility class for converting from one value to another.




* Class name: Conversions
* Namespace: Headzoo\Core



Constants
----------


### MINUTE
Number of seconds in a minute


```php
const MINUTE = 60
```





### HOUR
Number of seconds in an hour


```php
const HOUR = 3600
```





### DAY
Number of seconds in a day


```php
const DAY = 86400
```





### WEEK
Number of seconds in a week


```php
const WEEK = 604800
```





### MONTH
Number of seconds in a month


```php
const MONTH = 2630000
```





### YEAR
Number of seconds in a year


```php
const YEAR = 31560000
```





### KILOBYTE
Number of bytes in a kilobyte


```php
const KILOBYTE = 1024
```





### MEGABYTE
Number of bytes in a megabyte


```php
const MEGABYTE = 1048576
```





### GIGABYTE
Number of bytes in a gigabyte


```php
const GIGABYTE = 1073741824
```





### TERABYTE
Number of bytes in a terabyte


```php
const TERABYTE = 1099511627776.0
```





### PETABYTE
Number of bytes in a petabyte


```php
const PETABYTE = 1.1258999068426E+15
```





Properties
----------


### $byte_units
Byte size units


```php
protected array $byte_units = array("B", "KB", "MB", "GB", "TB", "PB")
```

* This property is **static**.


Methods
-------


### Headzoo\Core\Conversions::bytesToHuman
Converts a byte number into a human readable format

Examples:
```php
echo Conversions::bytesToHuman(100);
// Outputs: "100B"

echo Conversions::bytesToHuman(1024);
// Outputs: "1KB"

echo Conversions::bytesToHuman(1050);
// Outputs: "1.02KB"
```
```php
public string Headzoo\Core\Conversions::bytesToHuman(int $bytes, int $decimals)
```

* This method is **static**.

##### Arguments

* $bytes **int** - The number of bytes
* $decimals **int** - Number of decimal places to round to



### Headzoo\Core\Conversions::numberFormat
Format a number

Works exactly like the number_format() function. However, this method does not add decimal
places to whole numbers, and trailing 0 are removed.

Examples:
```php
echo Conversions::numberFormat(1024);
// Output: "1,024"

echo Conversions::numberFormat(1024.23);
// Outputs: "1,024.23"
```
```php
public string Headzoo\Core\Conversions::numberFormat(float $num, int $dec_max, string $dec_point, string $thousands_sep)
```

* This method is **static**.

##### Arguments

* $num **float** - The number to format
* $dec_max **int** - The maximum number of decimal places
* $dec_point **string** - The decimal point character
* $thousands_sep **string** - The thousands separator character


