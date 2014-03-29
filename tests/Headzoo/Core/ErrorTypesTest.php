<?php
use Headzoo\Core\ErrorTypes;

/**
 * @coversDefaultClass
 */
class ErrorTypesTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::isValid
     */
    public function testIsValid()
    {
        $this->assertTrue(ErrorTypes::isValid(E_ERROR));
        $this->assertTrue(ErrorTypes::isValid(E_ALL));
        $this->assertFalse(ErrorTypes::isValid(0));
        $this->assertFalse(ErrorTypes::isValid("foo"));
        $this->assertFalse(ErrorTypes::isValid(null));
    }
    
    /**
     * @covers ::getValue
     */
    public function testGetValue()
    {
        $this->assertEquals(
            E_ERROR,
            ErrorTypes::getValue(E_ERROR)
        );
        $this->assertEquals(
            E_ERROR,
            ErrorTypes::getValue("E_ERROR")
        );
    }

    /**
     * @covers ::getValue
     * @dataProvider providerGetValue
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testGetValue_Invalid($value)
    {
        ErrorTypes::getValue($value);
    }

    /**
     * @covers ::types
     */
    public function testTypes()
    {
        $this->assertNotEmpty(ErrorTypes::types());
        $this->assertContains(
            E_ERROR,
            ErrorTypes::types()
        );
        $this->assertContains(
            E_RECOVERABLE_ERROR,
            ErrorTypes::types()
        );
    }
    
    /**
     * Data provider for testGetValue_Invalid
     *
     * @return array
     */
    public function providerGetValue()
    {
        return [
            [0],
            ["e_error"],
            ["E_BAD"],
            [42],
            [null],
            [[]]
        ];
    }
}
