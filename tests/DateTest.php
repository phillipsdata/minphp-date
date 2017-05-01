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
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\Minphp\Date\Date', $this->getDate());
    }

    /**
     * @covers ::__construct
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
     * @covers ::cast
     * @covers ::format
     * @covers ::setDefaultTimezone
     * @covers ::toTime
     * @covers ::setTimezone
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
            array(null, 'Y', 'UTC', 'UTC', date('Y'))
        );
    }

    /**
     * @param string $startDate The start date
     * @param string $endDate The end date
     * @param array|null $formats The range formats
     * @param array $expected The expected output
     *
     * @covers ::__construct
     * @covers ::dateRange
     * @covers ::format
     * @covers ::toTime
     * @covers ::mergeArrays
     *
     * @dataProvider dateRangeProvider
     */
    public function testDateRange($startDate, $endDate, $formats, $expected)
    {
        $date = $this->getDate();

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
            array('2016-03-01', '2017-03-01', null, 'March 1, 2016 - March 1, 2017'),
            array('2016-02-02', '2016-02-03', null, 'February 2-3, 2016'),
            array('2016-02-02', '2016-01-01', null, 'February 2 - January 1, 2016'),
            array('2016-02-02', '2016-02-02', null, 'February 2, 2016'),
            array('2016-03-01', '2017-03-01', $formats, '2016-03-01| March 1, 2017'),
            array('2016-02-02', '2016-02-03', $formats, '02| 3, 2016'),
            array('2016-02-02', '2016-01-01', $formats, '2016| January 1, 2016'),
            array('2016-02-02', '2016-02-02', $formats, '2')
        );
    }

    /**
     * @param string|int $dateTime The date to cast to time
     * @param int $expected Expected output
     *
     * @covers ::__construct
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
     * @param array $expected Expected result
     *
     * @covers ::__construct
     * @covers ::getMonths
     *
     * @dataProvider monthsProvider
     */
    public function testGetMonths($start, $end, $keyFormat, $valueFormat, array $expected)
    {
        $date = $this->getDate();

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
                1, 12, 'm', 'n',
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
            array(3, 3, 'n', 'n', array('3' => '3')),
            array(4, 3, 'n', 'n', array())
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
     * @covers ::getYears
     *
     * @dataProvider yearsProvider
     */
    public function testGetYears($start, $end, $keyFormat, $valueFormat, array $expected)
    {
        $date = $this->getDate();

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
                2001, 2012, 'y', 'Y',
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
            array(2003, 2003, 'Y', 'Y', array('2003' => '2003')),
            array(2004, 2003, 'y', 'y', array())
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getTimezones
     * @covers ::timezoneFromIdentifier
     * @covers ::insertSortInsert
     * @covers ::insertionSort
     * @uses DateTimeZone
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
