# Minphp/Date

[![Build Status](https://travis-ci.org/phillipsdata/minphp-date.svg?branch=master)](https://travis-ci.org/phillipsdata/minphp-date) [![Coverage Status](https://coveralls.io/repos/github/phillipsdata/minphp-date/badge.svg?branch=master)](https://coveralls.io/github/phillipsdata/minphp-date?branch=master)

Date manipulation library.

## Installation

Install via composer:

```sh
composer require minphp/date
```

## Basic Usage

### Instantiation

You may optionally define custom datetime formats and from/to timezones for dates during instantiation.
However, they can be defined separately when [setting timezones](#setting-timezones) or [setting datetime formats](#setting-datetime-formats).

```php
use \Minphp\Date\Date;

// Define a new 'date' format to use when formatting dates via Date::cast
$formats = array('date' => 'M d, Y');

// Define timezones to use when working with dates
// The $fromTimezone is the timezone of any given date unless it contains timezone information itself
$fromTimezone = 'UTC';
// The $toTimezone is the timezone of formatted date output
$toTimezone = 'America/Los_Angeles';

$date = new Date($formats, $fromTimezone, $toTimezone);
```

### Setting Timezones

Timezones can be set during instantiation or via ::setTimezone.
Not setting a set of from/to timezones will use the system's defined timezone unless the given date contains timezone information.

```php
use \Minphp\Date\Date;

$date = new Date();

$fromTimezone = 'UTC';
$toTimezone = 'America/Los_Angeles';

$date->setTimezone($fromTimezone, $toTimezone);
```

### Setting Datetime Formats

Date formats can be set on the object to simplify calls when formatting dates.

The following date formats are available by default, but can be overridden, or added to, using this method:

```
'date' => 'F j, Y'
'day' => 'l, F j, Y'
'month' => 'F Y'
'year' => 'Y'
'date_time' => 'M d y g:i:s A'
```

```php
use \Minphp\Date\Date;

$date = new Date();

// Define new formats to use
$formats = array(
    'date' => 'M d, Y',
    'day' => 'd',
    'month' => 'F',
    'year' => 'y',
    'date_time' => 'M d, Y h:i A',
    'full_date' => 'c'
);

$date->setFormats($formats);
```

### Formatting a Date

Dates can be formatted by specifying your own [php date format](http://php.net/manual/en/function.date.php)
or by using one of the [predefined formats](#setting-datetime-formats).

To format a date by specifying your own [date format](http://php.net/manual/en/function.date.php), use Date::format:

```php
use \Minphp\Date\Date;

$date = new Date(null, 'Europe/Paris', 'Europe/Paris');

$unformattedDate = '1944-06-06T06:00:00+02:00';
$formattedDate = $date->format('M d, Y H:i', $unformattedDate); // Jun 06, 1944 06:00
```

To format a date via a [predefined format](#setting-datetime-formats) or by specifying
your own [date format](http://php.net/manual/en/function.date.php), use Date::cast:

```php
use \Minphp\Date\Date;

// Define new formats to use
$formats = array(
    'date' => 'M d, Y',
    'day' => 'd',
    'month' => 'F',
    'year' => 'y',
    'date_time' => 'M d, Y h:i A',
    'full_date' => 'c'
);

$date = new Date($formats, 'Europe/Paris', 'Europe/Paris');

$unformattedDate = '1944-06-06T06:00:00+02:00';
$formattedDate = $date->cast($unformattedDate); // Jun 06, 1944
$formattedDatetime = $date->cast($unformattedDate, 'date_time'); // Jun 06, 1944 06:00 AM
$formattedDay = $date->cast($unformattedDate, 'day'); // 06
$formattedYear = $date->cast($unformattedDate, 'year'); // 44
$formatted = $date->cast($unformattedDate, 'Ymd'); // 19440606
```

Formatting a date respects the set timezones:

```php
use \Minphp\Date\Date;

$date = new Date(null, 'America/New_York', 'America/Los_Angeles');

// Convert date from New York to Los Angeles time
$unformattedDate = '2001-09-11 08:46:40';
$formattedDate = $date->cast($unformattedDate, 'date_time'); // Sep 11, 2001 5:46:40 AM
```

### Retrieving a Date Range

A set of months or years can be generated. The dates are created from the current server time in the defined [from timezone](#setting-timezones).

#### Generating Months
```php
use \Minphp\Date\Date;

$date = new Date(null, 'UTC');

print_r($date->getMonths());
```

Output:

```php
Array (
    [01] => January
    [02] => February
    [03] => March
    [04] => April
    [05] => May
    [06] => June
    [07] => July
    [08] => August
    [09] => September
    [10] => October
    [11] => November
    [12] => December
)
```

The month range and key/value format can be specified:

```php
use \Minphp\Date\Date;

$date = new Date(null, 'UTC');

print_r($date->getMonths(2, 4, 'n', 'M'));
```

Output:

```php
Array (
    [02] => Feb
    [03] => Mar
    [04] => Apr
)
```

#### Generating Years
```php
use \Minphp\Date\Date;

$date = new Date(null, 'UTC');

print_r($date->getYears(2010, 2013));
```

Output:

```php
Array (
    [10] => 2010
    [11] => 2011
    [12] => 2012
    [13] => 2013
)
```

The year key/value format can be specified:

```php
use \Minphp\Date\Date;

$date = new Date(null, 'UTC');

print_r($date->getYears(2010, 2013, 'Y', 'y'));
```

Output:

```php
Array (
    [2010] => 10
    [2011] => 11
    [2012] => 12
    [2013] => 13
)
```

### Generating a Date Range

A date range can be generated between two given dates, and formatted according to the given formatting rules.
The dates are created from the current server time in the defined [from timezone](#setting-timezones).

The following date range formats are used by default, but can be overridden:

```
'start' => array(
    'same_day' => 'F j, Y',
    'same_month' => 'F j-',
    'same_year' => 'F j - ',
    'other' => 'F j, Y - '
),
'end' => array(
    'same_day' => '',
    'same_month' => 'j, Y',
    'same_year' => 'F j, Y',
    'other' => 'F j, Y'
)
```

```php
use \Minphp\Date\Date;

$date = new Date(null, 'UTC');

$dayRange = $date->dateRange('2016-06-06', '2016-06-06')); // June 6, 2016
$monthRange = $date->dateRange('2016-06-06', '2016-06-24')); // June 6-24, 2016
$yearRange = $date->dateRange('2016-06-06', '2016-07-07')); // June 6 - July 7, 2016
$otherRange = $date->dateRange('2016-06-06', '2017-07-07')); // June 6, 2016 - July 7, 2017
```

### Retrieving a Unix Timestamp

A Unix Timestamp can be retrieved from a date via [strtotime](http://php.net/manual/en/function.strtotime.php).
However, the method considers the system's timezone--not the defined [from timezone](#setting-timezones)--for the timestamp conversion.

```php
use \Minphp\Date\Date;

$date = new Date();

$unixDate = $date->toTime('2016-01-01T12:00:00+00:00'); // 1451649600
$unixTime = $date->toTime(1451649600); // 1451649600
```
