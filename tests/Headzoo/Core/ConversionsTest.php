<?php
use Headzoo\Core\Conversions;

class ConversionsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::numberFormat
     * @dataProvider providerNumberFormat
     */
    public function testNumberFormat($num, $expected)
    {
        $this->assertEquals(
            $expected,
            Conversions::numberFormat($num)
        );
    }
    
    /**
     * @covers Headzoo\Core\Conversions::bytesToHuman
     * @dataProvider providerBytesToHuman
     */
    public function testBytesToHuman($bytes, $expected)
    {
        $this->assertEquals(
            $expected,
            Conversions::bytesToHuman($bytes)
        );
    }
    
    /**
     * Data provider for testNumberFormat
     *
     * @return array
     */
    public function providerNumberFormat()
    {
        return [
            [1024,      "1,024"],
            [1024.23,   "1,024.23"],
            [100.23,    "100.23"],
            ["test",    "0"]
        ];
    }

    /**
     * Data provider for testBytesToHuman
     * 
     * @return array
     */
    public function providerBytesToHuman()
    {
        return [
            [1,                                     "1B"],
            [500,                                   "500B"],
            [1000,                                  "1,000B"],
            [Conversions::BYTES_KILOBYTE,            "1KB"],
            [Conversions::BYTES_KILOBYTE + 25,       "1.02KB"],
            [Conversions::BYTES_MEGABYTE,            "1MB"],
            [Conversions::BYTES_MEGABYTE + 11000,    "1.01MB"],
            [Conversions::BYTES_GIGABYTE,            "1GB"],
            [Conversions::BYTES_GIGABYTE + 111111000,"1.1GB"],
            [Conversions::BYTES_TERABYTE,            "1TB"],
            [Conversions::BYTES_PETABYTE,            "1PB"]
        ];
    }
}
