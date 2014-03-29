<?php
use Headzoo\Core\Errors;

/**
 * @coversDefaultClass
 */
class ErrorsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::isValid
     */
    public function testIsValid()
    {
        $this->assertTrue(Errors::isValid(E_ERROR));
        $this->assertTrue(Errors::isValid(E_ALL));
        $this->assertFalse(Errors::isValid(0));
        $this->assertFalse(Errors::isValid("foo"));
        $this->assertFalse(Errors::isValid(null));
    }
    
    /**
     * @covers ::getValue
     */
    public function testGetValue()
    {
        $this->assertEquals(
            E_ERROR,
            Errors::getValue(E_ERROR)
        );
        $this->assertEquals(
            E_ERROR,
            Errors::getValue("E_ERROR")
        );
    }

    /**
     * @covers ::getValue
     * @dataProvider providerGetValue
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testGetValue_Invalid($value)
    {
        Errors::getValue($value);
    }

    /**
     * @covers ::types
     */
    public function testTypes()
    {
        $this->assertNotEmpty(Errors::types());
        $this->assertContains(
            E_ERROR,
            Errors::types()
        );
        $this->assertContains(
            E_RECOVERABLE_ERROR,
            Errors::types()
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
