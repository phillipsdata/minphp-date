<?php
namespace minphp\Date;

use \PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \minphp\Date\Date
 */
class DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('minphp\Date\Date', new Date());
    }
}
