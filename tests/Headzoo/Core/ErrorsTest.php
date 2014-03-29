<?php
use Headzoo\Core\Errors;

/**
 * @coversDefaultClass
 */
class ErrorsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::isError
     */
    public function testIsError()
    {
        $this->assertTrue(Errors::isError(E_ERROR));
        $this->assertTrue(Errors::isError(E_ALL));
        $this->assertFalse(Errors::isError("E_ERROR"));
        $this->assertFalse(Errors::isError(0));
        $this->assertFalse(Errors::isError("foo"));
        $this->assertFalse(Errors::isError(null));
    }
    
    /**
     * @covers ::toInteger
     */
    public function testToInteger()
    {
        $this->assertEquals(
            E_ERROR,
            Errors::toInteger(E_ERROR)
        );
        $this->assertEquals(
            E_ERROR,
            Errors::toInteger("E_ERROR")
        );
    }

    /**
     * @covers ::toInteger
     * @dataProvider providerToInteger
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testToInteger_Invalid($value)
    {
        Errors::toInteger($value);
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        $this->assertNotEmpty(Errors::toArray());
        $this->assertContains(
            E_ERROR,
            Errors::toArray()
        );
        $this->assertContains(
            E_RECOVERABLE_ERROR,
            Errors::toArray()
        );
    }
    
    /**
     * Data provider for testToInteger_Invalid
     *
     * @return array
     */
    public function providerToInteger()
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
