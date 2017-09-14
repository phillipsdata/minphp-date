<?php
namespace Minphp\Date\Tests;

use PHPUnit_Framework_TestCase;
use Minphp\Date\Date;

/**
 * @coversDefaultClass \Minphp\Date\Date
 */
class DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @uses \Minphp\Date\Date::setTimezone
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\Minphp\Date\Date', $this->getDate());
    }

    /**
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @covers ::setTimezone
     */
    public function testSetTimezone()
    {
        $date = $this->getDate();

        $this->assertInstanceOf('\Minphp\Date\Date', $date->setTimezone('UTC', 'America/Los_Angeles'));
    }

    /**
     * @param array|null The formats to set
     *
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setTimezone
     * @covers ::setFormats
     *
     * @dataProvider formatProvider
     */
    public function testSetFormats($formats)
    {
        $date = $this->getDate();

        $this->assertInstanceOf('\Minphp\Date\Date', $date->setFormats($formats));
    }

    /**
     * Data provider for ::testSetFormats
     *
     * @return array
     */
    public function formatProvider()
    {
        return array(
            array(null),
            array(array('day' => 'd'))
        );
    }

    /**
     * @param string $fromDate The date to convert
     * @param string $format The format of the converted date to fetch
     * @param string $fromTimezone The $fromDate's timezone
     * @param string $toTimezone The timezone to convert to
     * @param string $expected The expected result
     *
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @covers ::cast
     * @covers ::format
     * @covers ::toTime
     * @covers ::setTimezone
     * @covers ::dateTime
     * @covers ::dateTimeZone
     *
     * @dataProvider castProvider
     */
    public function testCast($fromDate, $format, $fromTimezone, $toTimezone, $expected)
    {
        $date = $this->getDate();

        $date->setTimezone($fromTimezone, $toTimezone);

        $this->assertEquals($expected, $date->cast($fromDate, $format));
    }

    /**
     * Data provider for ::testCast
     *
     * @return array
     */
    public function castProvider()
    {
        return array(
            array('2016-05-12T00:00:00+00:00', 'Y-m-d', 'UTC', 'UTC', '2016-05-12'),
            array('2016-05-12T07:00:00+00:00', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-05-12 07:00:00'),
            array('2016-05-12T00:00:00+00:00', 'O', 'UTC', 'UTC', '+0000'),
            array('2016-05-12T00:00:00+00:00', 'Y-m-d', 'UTC', 'America/Los_Angeles', '2016-05-11'),
            array('2016-05-12T07:00:00+00:00', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-05-12 00:00:00'),
            array('2016-05-12T00:00:00+00:00', 'O', 'UTC', 'America/Los_Angeles', '-0700'),
            array('2016-05-12T00:00:00-07:00', 'Y-m-d', 'America/Los_Angeles', 'UTC', '2016-05-12'),
            array('2016-05-12T00:00:00-07:00', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-05-12 07:00:00'),
            array('2016-12-12T00:00:00-08:00', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-12-12 08:00:00'),
            array('2016-05-12 00:00:00', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-05-12 07:00:00'),
            array('2016-12-12 00:00:00', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-12-12 08:00:00'),
            array('2016-05-12 07:00:00', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-05-12 00:00:00'),
            array('2016-12-12 08:00:00', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-12-12 00:00:00'),
            array(null, 'Y', 'UTC', 'UTC', date('Y')),
            array('2016-12-12 05:00:00', 'Y-m-d H:i:s', null, null, '2016-12-12 05:00:00')
        );
    }

    /**
     * @param string $fromDate The date to convert
     * @param string $modifier The strtotime-compatible modifier (e.g. +1 month)
     * @param string $format The format of the converted date to fetch
     * @param string $fromTimezone The $fromDate's timezone
     * @param string $toTimezone The timezone to convert to
     * @param string $expected The expected result
     *
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @covers ::modify
     * @covers ::cast
     * @covers ::format
     * @covers ::toTime
     * @covers ::setTimezone
     * @covers ::dateTime
     * @covers ::dateTimeZone
     *
     * @dataProvider modifyProvider
     */
    public function testModify($fromDate, $modifier, $format, $fromTimezone, $toTimezone, $expected)
    {
        $date = $this->getDate();

        $date->setTimezone($fromTimezone, $toTimezone);

        $this->assertEquals($expected, $date->modify($fromDate, $modifier, $format));
    }

    /**
     * Data provider for ::testModify
     *
     * @return array
     */
    public function modifyProvider()
    {
        return array(
            array('2016-05-12T00:00:00+00:00', '+1 second', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-05-12 00:00:01'),
            array('2016-05-12T00:00:00+00:00', '+1 minute', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-05-12 00:01:00'),
            array('2016-05-12T00:00:00+00:00', '+1 hour', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-05-12 01:00:00'),
            array('2016-05-12T00:00:00+00:00', '+1 day', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-05-13 00:00:00'),
            array('2016-05-12T00:00:00+00:00', '+1 week', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-05-19 00:00:00'),
            array('2016-05-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-06-12 00:00:00'),
            array('2016-06-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-07-12 00:00:00'),
            array('2016-05-12T00:00:00+00:00', '+1 year', 'Y-m-d H:i:s', 'UTC', 'UTC', '2017-05-12 00:00:00'),
            array(
                '2016-05-12 00:00:00',
                '+2 days -1 second',
                'Y-m-d H:i:s',
                'America/Los_Angeles',
                'UTC',
                '2016-05-14 06:59:59'
            ),
            array(null, 'May 10, 2017 midnight', 'c', 'America/Los_Angeles', 'UTC', '2017-05-10T07:00:00+00:00'),
            array(null, 'May 10, 2017 midnight', 'c', 'UTC', 'America/Los_Angeles', '2017-05-09T17:00:00-07:00'),
            array(
                '2016-05-12T00:00:00+00:00',
                'yesterday',
                'Y-m-d H:i:s',
                'UTC',
                'America/Los_Angeles',
                '2016-05-10 17:00:00'
            ),
            // Timezone in date takes precedence over the from_timezone of America/Los_Angeles
           array(
                '2016-05-12T00:00:00+00:00',
                '-7 day',
                'M d, Y H:i O',
                'America/Los_Angeles',
                'America/Los_Angeles',
                'May 04, 2016 17:00 -0700'
            ),
            // 1 month is 30 days, not next numerical month
            array('2016-01-31', '+1 month', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-03-01 16:00:00'),
            // Crossover to daylight savings
            array('2016-03-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-04-11 16:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'c', 'UTC', 'America/Los_Angeles', '2016-04-12T00:00:00-07:00'),
            array('2016-03-12T00:00:00', '+1 month', 'c', 'UTC', 'America/Los_Angeles', '2016-04-11T16:00:00-07:00'),
            array('2016-03-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-04-12 08:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'c', 'America/Los_Angeles', 'UTC', '2016-04-12T08:00:00+00:00'),
            array('2016-03-12T00:00:00', '+1 month', 'c', 'America/Los_Angeles', 'UTC', '2016-04-12T07:00:00+00:00'),
            array('2016-03-12T00:00:00Z', '+1 month', 'c', 'America/Los_Angeles', 'UTC', '2016-04-12T00:00:00+00:00'),

            array('2016-03-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Australia/Sydney', '2016-04-12 11:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Australia/Sydney', '2016-04-12 19:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'c', 'UTC', 'Australia/Sydney', '2016-04-12T19:00:00+10:00'),
            array('2016-03-12T00:00:00', '+1 month', 'c', 'UTC', 'Australia/Sydney', '2016-04-12T11:00:00+10:00'),
            array('2016-03-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'Australia/Sydney', 'UTC', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'Australia/Sydney', 'UTC', '2016-04-12 08:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'c', 'Australia/Sydney', 'UTC', '2016-04-12T08:00:00+00:00'),
            array('2016-03-12T00:00:00', '+1 month', 'c', 'Australia/Sydney', 'UTC', '2016-04-11T14:00:00+00:00'),
            array('2016-03-12T00:00:00Z', '+1 month', 'c', 'Australia/Sydney', 'UTC', '2016-04-12T00:00:00+00:00'),

            array('2016-03-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Europe/Paris', '2016-04-12 01:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Europe/Paris', '2016-04-12 09:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'c', 'UTC', 'Europe/Paris', '2016-04-12T09:00:00+02:00'),
            array('2016-03-12T00:00:00', '+1 month', 'c', 'UTC', 'Europe/Paris', '2016-04-12T01:00:00+02:00'),
            array('2016-03-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'Europe/Paris', 'UTC', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'Europe/Paris', 'UTC', '2016-04-12 08:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'c', 'Europe/Paris', 'UTC', '2016-04-12T08:00:00+00:00'),
            array('2016-03-12T00:00:00', '+1 month', 'c', 'Europe/Paris', 'UTC', '2016-04-11T22:00:00+00:00'),
            array('2016-03-12T00:00:00Z', '+1 month', 'c', 'Europe/Paris', 'UTC', '2016-04-12T00:00:00+00:00'),
            // Crossover from daylight savings
            array('2016-10-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-11-12 00:00:00'),
            array('2016-10-12T00:00:00-07:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-11-12 07:00:00'),
            array('2016-10-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-11-11 17:00:00'),
            array('2016-10-12T00:00:00-07:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'America/Los_Angeles', '2016-11-12 00:00:00'),
            array('2016-10-12T00:00:00-07:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Australia/Sydney', '2016-11-12 18:00:00'),
            array('2016-10-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Australia/Sydney', '2016-11-12 11:00:00'),
            array('2016-10-12T00:00:00-07:00', '+1 month', 'c', 'UTC', 'America/Los_Angeles', '2016-11-12T00:00:00-08:00'),
            array('2016-10-12T00:00:00', '+1 month', 'c', 'UTC', 'America/Los_Angeles', '2016-11-11T17:00:00-08:00'),
            array('2016-10-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-11-12 00:00:00'),
            array('2016-10-12T00:00:00-07:00', '+1 month', 'Y-m-d H:i:s', 'America/Los_Angeles', 'UTC', '2016-11-12 07:00:00'),
            array('2016-10-12T00:00:00-07:00', '+1 month', 'c', 'America/Los_Angeles', 'UTC', '2016-11-12T07:00:00+00:00'),
            array('2016-10-12T00:00:00', '+1 month', 'c', 'America/Los_Angeles', 'UTC', '2016-11-12T08:00:00+00:00'),
            array('2016-10-12T00:00:00Z', '+1 month', 'c', 'America/Los_Angeles', 'UTC', '2016-11-12T00:00:00+00:00'),

            array('2016-09-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Australia/Sydney', '2016-10-12 10:00:00'),
            array('2016-09-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Australia/Sydney', '2016-10-12 18:00:00'),
            array('2016-09-12T00:00:00-08:00', '+1 month', 'c', 'UTC', 'Australia/Sydney', '2016-10-12T18:00:00+11:00'),
            array('2016-09-12T00:00:00', '+1 month', 'c', 'UTC', 'Australia/Sydney', '2016-10-12T10:00:00+11:00'),
            array('2016-09-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'Australia/Sydney', 'UTC', '2016-10-12 00:00:00'),
            array('2016-09-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'Australia/Sydney', 'UTC', '2016-10-12 08:00:00'),
            array('2016-09-12T00:00:00-08:00', '+1 month', 'c', 'Australia/Sydney', 'UTC', '2016-10-12T08:00:00+00:00'),
            array('2016-09-12T00:00:00', '+1 month', 'c', 'Australia/Sydney', 'UTC', '2016-10-11T13:00:00+00:00'),
            array('2016-09-12T00:00:00Z', '+1 month', 'c', 'Australia/Sydney', 'UTC', '2016-10-12T00:00:00+00:00'),

            array('2016-10-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Europe/Paris', '2016-11-12 02:00:00'),
            array('2016-10-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'Europe/Paris', '2016-11-12 10:00:00'),
            array('2016-10-12T00:00:00-08:00', '+1 month', 'c', 'UTC', 'Europe/Paris', '2016-11-12T10:00:00+01:00'),
            array('2016-10-12T00:00:00', '+1 month', 'c', 'UTC', 'Europe/Paris', '2016-11-12T02:00:00+01:00'),
            array('2016-10-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'Europe/Paris', 'UTC', '2016-11-12 00:00:00'),
            array('2016-10-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'Europe/Paris', 'UTC', '2016-11-12 08:00:00'),
            array('2016-10-12T00:00:00-08:00', '+1 month', 'c', 'Europe/Paris', 'UTC', '2016-11-12T08:00:00+00:00'),
            array('2016-10-12T00:00:00', '+1 month', 'c', 'Europe/Paris', 'UTC', '2016-11-11T23:00:00+00:00'),
            array('2016-10-12T00:00:00Z', '+1 month', 'c', 'Europe/Paris', 'UTC', '2016-11-12T00:00:00+00:00'),
            // Crossover timezone between daylight savings
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'America/Los_Angeles', 'America/Los_Angeles', '2016-04-12 00:00:00'),
            array('2016-10-12T00:00:00-07:00', '+1 month', 'Y-m-d H:i:s', 'America/Los_Angeles', 'America/Los_Angeles', '2016-11-12 00:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'c', 'America/Los_Angeles', 'America/Los_Angeles', '2016-04-12T00:00:00-07:00'),
            array('2016-03-12T00:00:00+11:00', '+1 month', 'Y-m-d H:i:s', 'Australia/Sydney', 'Australia/Sydney', '2016-04-12 00:00:00'),
            array('2016-09-12T00:00:00+10:00', '+1 month', 'Y-m-d H:i:s', 'Australia/Sydney', 'Australia/Sydney', '2016-10-12 00:00:00'),
            array('2016-03-12T00:00:00+01:00', '+1 month', 'Y-m-d H:i:s', 'Europe/Paris', 'Europe/Paris', '2016-04-12 00:00:00'),
            array('2016-10-12T00:00:00+02:00', '+1 month', 'Y-m-d H:i:s', 'Europe/Paris', 'Europe/Paris', '2016-11-12 00:00:00'),
            // No daylight savings observed
            array('2016-03-12T00:00:00+00:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00-08:00', '+1 month', 'Y-m-d H:i:s', 'UTC', 'UTC', '2016-04-12 08:00:00'),
            array('2016-03-12T00:00:00-03:00', '+1 month', 'Y-m-d H:i:s', 'America/Argentina/Buenos_Aires', 'America/Argentina/Buenos_Aires', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00+02:00', '+1 month', 'Y-m-d H:i:s', 'Africa/Cairo', 'Africa/Cairo', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00+08:00', '+1 month', 'Y-m-d H:i:s', 'Australia/Perth', 'Australia/Perth', '2016-04-12 00:00:00'),
            array('2016-03-12T00:00:00-10:00', '+1 month', 'Y-m-d H:i:s', 'Pacific/Honolulu', 'Pacific/Honolulu', '2016-04-12 00:00:00'),
            // Between different timezones
            array('2016-06-06T06:00:00+02:00', '+1 month', 'c', 'Europe/Paris', 'America/Los_Angeles', '2016-07-05T21:00:00-07:00'),
            array('2016-07-05T21:00:00-07:00', '-1 month', 'c', 'America/Los_Angeles', 'Europe/Paris', '2016-06-06T06:00:00+02:00'),
            array('2016-10-12T00:00:00', '+1 month', 'c', 'Europe/Paris', 'America/Los_Angeles', '2016-11-11T16:00:00-08:00'),
            array('2016-10-12T00:00:00', '+1 month', 'c', 'America/Los_Angeles', 'Australia/Sydney', '2016-11-12T19:00:00+11:00'),
        );
    }

    /**
     * @param string $startDate The start date
     * @param string $endDate The end date
     * @param array|null $formats The range formats
     * @param string $timezone The start timezone
     * @param array $expected The expected output
     *
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @covers ::dateRange
     * @covers ::format
     * @covers ::toTime
     * @covers ::mergeArrays
     * @covers ::setTimezone
     * @covers ::dateTime
     * @covers ::dateTimeZone
     *
     * @dataProvider dateRangeProvider
     */
    public function testDateRange($startDate, $endDate, $formats, $timezone, $expected)
    {
        $date = $this->getDate();

        // Set the 'from' timezone
        $date->setTimezone($timezone);

        $this->assertEquals($expected, $date->dateRange($startDate, $endDate, $formats));
    }

    /**
     * Data Provider for ::testDateRange
     *
     * @return array
     */
    public function dateRangeProvider()
    {
        $formats = array(
            'start' => array(
                'same_day' => 'd',
                'same_month' => 'm| ',
                'same_year' => 'Y| ',
                'other' => 'Y-m-d| '
            )
        );

        return array(
            array('2016-03-01', '2017-03-01', null, null, 'March 1, 2016 - March 1, 2017'),
            array('2016-02-02', '2016-02-03', null, null, 'February 2-3, 2016'),
            array('2016-02-02', '2016-01-01', null, null, 'February 2 - January 1, 2016'),
            array('2016-02-02', '2016-02-02', null, null, 'February 2, 2016'),
            array('2016-03-01', '2017-03-01', $formats, null, '2016-03-01| March 1, 2017'),
            array('2016-02-02', '2016-02-03', $formats, null, '02| 3, 2016'),
            array('2016-02-02', '2016-01-01', $formats, null, '2016| January 1, 2016'),
            array('2016-02-02', '2016-02-02', $formats, null, '2'),
            array(1483228800, 1514764800, null, 'UTC', 'January 1, 2017 - January 1, 2018'),
            array(1483228800, '2017-01-01', null, 'UTC', 'January 1, 2017'),
            array(1483228800, '2017-01-03', null, 'UTC', 'January 1-3, 2017'),
            array(1483228800, '2017-02-02', null, 'UTC', 'January 1 - February 2, 2017'),
            array(1483228800, 1514764800, null, 'America/Los_Angeles', 'December 31, 2016 - December 31, 2017'),
            array(1483228800, '2017-01-01', null, 'America/Los_Angeles', 'December 31, 2016 - January 1, 2017'),
            array(1483228800, '2017-01-03', null, 'America/Los_Angeles', 'December 31, 2016 - January 3, 2017'),
            array(1483228800, '2017-02-02', null, 'America/Los_Angeles', 'December 31, 2016 - February 2, 2017'),
            array(
                1483228800,
                '2017-02-02T00:00:00-08:00',
                null,
                'America/Los_Angeles',
                'December 31, 2016 - February 2, 2017'
            )
        );
    }

    /**
     * @param string|int $dateTime The date to cast to time
     * @param int $expected Expected output
     *
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @uses \Minphp\Date\Date::setTimezone
     * @covers ::toTime
     *
     * @dataProvider timeProvider
     */
    public function testToTime($dateTime, $expected)
    {
        $date = $this->getDate();

        $this->assertEquals($expected, $date->toTime($dateTime));
    }

    /**
     * Data Provider for ::testToTime
     *
     * @return array
     */
    public function timeProvider()
    {
        return array(
            array('2016-01-01T00:00:00-07:00', 1451631600),
            array('2016-01-01T00:00:00+00:00', 1451606400),
            array('2017-11-15T00:00:00+12:00', 1510660800),
            array(1, 1),
            array(100000, 100000)
        );
    }

    /**
     * @param int $start Start month
     * @param int $end End month
     * @param string $keyFormat Array key format
     * @param string $valueFormat Array value format
     * @param string $timezone The timezone
     * @param array $expected Expected result
     *
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @covers ::getMonths
     * @covers ::setTimezone
     * @covers ::dateTime
     * @covers ::dateTimeZone
     *
     * @dataProvider monthsProvider
     */
    public function testGetMonths($start, $end, $keyFormat, $valueFormat, $timezone, array $expected)
    {
        $date = $this->getDate();

        // Set the 'from' timezone
        $date->setTimezone($timezone);

        $this->assertEquals($expected, $date->getMonths($start, $end, $keyFormat, $valueFormat));
    }

    /**
     * Data provider for ::testGetMonths
     *
     * @return array
     */
    public function monthsProvider()
    {
        return array(
            array(
                1, 12, 'm', 'n', null,
                array(
                    '01' => '1',
                    '02' => '2',
                    '03' => '3',
                    '04' => '4',
                    '05' => '5',
                    '06' => '6',
                    '07' => '7',
                    '08' => '8',
                    '09' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12'
                )
            ),
            array(
                1, 24, 'm', 'n', 'UTC',
                array(
                    '01' => '1',
                    '02' => '2',
                    '03' => '3',
                    '04' => '4',
                    '05' => '5',
                    '06' => '6',
                    '07' => '7',
                    '08' => '8',
                    '09' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12'
                )
            ),
            array(3, 3, 'n', 'n', null, array('3' => '3')),
            array(4, 3, 'n', 'n', 'UTC', array()),
            array(
                1, 12, 'm', 'n', 'America/Los_Angeles',
                array(
                    '01' => '1',
                    '02' => '2',
                    '03' => '3',
                    '04' => '4',
                    '05' => '5',
                    '06' => '6',
                    '07' => '7',
                    '08' => '8',
                    '09' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12'
                )
            )
        );
    }

    /**
     * @param int $start Start month
     * @param int $end End month
     * @param string $keyFormat Array key format
     * @param string $valueFormat Array value format
     * @param array $expected Expected result
     *
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @covers ::getYears
     * @covers ::setTimezone
     * @covers ::dateTime
     * @covers ::dateTimeZone
     *
     * @dataProvider yearsProvider
     */
    public function testGetYears($start, $end, $keyFormat, $valueFormat, $timezone, array $expected)
    {
        $date = $this->getDate();

        // Set the 'from' timezone
        $date->setTimezone($timezone);

        $this->assertEquals($expected, $date->getYears($start, $end, $keyFormat, $valueFormat));
    }

    /**
     * Data provider for ::testGetYears
     *
     * @return array
     */
    public function yearsProvider()
    {
        return array(
            array(
                2001, 2012, 'y', 'Y', null,
                array(
                    '01' => '2001',
                    '02' => '2002',
                    '03' => '2003',
                    '04' => '2004',
                    '05' => '2005',
                    '06' => '2006',
                    '07' => '2007',
                    '08' => '2008',
                    '09' => '2009',
                    '10' => '2010',
                    '11' => '2011',
                    '12' => '2012'
                )
            ),
            array(2003, 2003, 'Y', 'Y', null, array('2003' => '2003')),
            array(2004, 2003, 'y', 'y', 'UTC', array()),
            array(
                2001, 2012, 'y', 'Y', 'America/Los_Angeles',
                array(
                    '01' => '2001',
                    '02' => '2002',
                    '03' => '2003',
                    '04' => '2004',
                    '05' => '2005',
                    '06' => '2006',
                    '07' => '2007',
                    '08' => '2008',
                    '09' => '2009',
                    '10' => '2010',
                    '11' => '2011',
                    '12' => '2012'
                )
            ),
        );
    }

    /**
     * @covers ::__construct
     * @uses \Minphp\Date\Date::setFormats
     * @uses \Minphp\Date\Date::setTimezone
     * @covers ::getTimezones
     * @covers ::timezoneFromIdentifier
     * @covers ::insertSortInsert
     * @covers ::insertionSort
     * @covers ::dateTimeZone
     *
     * @dataProvider timezoneProvider
     */
    public function testGetTimezones($country)
    {
        $date = $this->getDate();
        $timezones = $date->getTimezones($country);

        // There should be timezones
        $this->assertNotEmpty($timezones);

        // A subset of the timezones should be given if a country is set
        if ($country) {
            $allTimezones = $date->getTimezones();
            $this->assertLessThan(count($allTimezones), count($timezones));
        }

        // Each timezone should consist of a set of keys
        $keys = array('id', 'name', 'offset', 'utc', 'zone');
        foreach ($timezones as $timezone) {
            foreach ($timezone as $data) {
                foreach ($keys as $key) {
                    $this->assertArrayHasKey($key, $data);
                }
            }
        }
    }

    /**
     * Data provider for ::testGetTimezones
     *
     * @return array
     */
    public function timezoneProvider()
    {
        return array(
            array(null),
            array('US'),
            array('')
        );
    }

    /**
     * Retrieves an instance of \Minphp\Date\Date
     *
     * @return \Minphp\Date\Date
     */
    private function getDate()
    {
        return new Date();
    }
}
