<?php
use Headzoo\Core\Errors;

/**
 * @coversDefaultClass
 */
class ErrorsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::isTrueError
     * @dataProvider providerIsTrueError
     */
    public function testIsTrueError($value, $expected)
    {
        $this->assertEquals(
            $expected, 
            Errors::isTrueError($value)
        );
    }

    /**
     * @covers ::isTrueUser
     * @dataProvider providerIsTrueUser
     */
    public function testIsTrueUser($value, $expected)
    {
        $this->assertEquals(
            $expected,
            Errors::isTrueUser($value)
        );
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
     * @covers ::toString
     */
    public function testToString()
    {
        $this->assertEquals(
            "E_ERROR",
            Errors::toString(E_ERROR)
        );
        $this->assertEquals(
            "E_ERROR",
            Errors::toString("E_ERROR")
        );
    }

    /**
     * @covers ::toString
     * @dataProvider providerToString
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testToString_Invalid($value)
    {
        Errors::toString($value);
    }
    
    /**
     * @covers ::toUser
     * @dataProvider providerToUser
     */
    public function testToUser($in, $out, $expected)
    {
        $this->assertEquals(
            $expected,
            Errors::toUser($in)
        );
        $this->assertEquals(
            $in,
            $out
        );
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
     * Data provider for testIsTrueError
     *
     * @return array
     */
    public function providerIsTrueError()
    {
        return [
            [E_ERROR,       true],
            [E_ALL,         true],
            [E_USER_ERROR,  true],
            ["E_ERROR",     false],
            [0,             false],
            ["e_error",     false],
            [null,          false]
        ];
    }

    /**
     * Data provider for testIsTrueUser
     *
     * @return array
     */
    public function providerIsTrueUser()
    {
        return [
            [E_USER_ERROR,      true],
            [E_USER_WARNING,    true],
            [E_USER_NOTICE,     true],
            [E_USER_DEPRECATED, true],
            [E_ERROR,           false],
            [E_WARNING,         false],
            [0,                 false],
            ["e_user_error",    false]
        ];
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

    /**
     * Data provider for testToString_Invalid
     *
     * @return array
     */
    public function providerToString()
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
    
    /**
     * Data provider for testToUser
     *
     * @return array
     */
    public function providerToUser()
    {
        return [
            [E_ERROR,           E_USER_ERROR,       true],
            [E_WARNING,         E_USER_WARNING,     true],
            [E_NOTICE,          E_USER_NOTICE,      true],
            [E_DEPRECATED,      E_USER_DEPRECATED,  true],
            [E_USER_ERROR,      E_USER_ERROR,       true],
            [E_USER_WARNING,    E_USER_WARNING,     true],
            [E_USER_NOTICE,     E_USER_NOTICE,      true],
            [E_USER_DEPRECATED, E_USER_DEPRECATED,  true],
            [E_PARSE,           E_PARSE,            false],
            [E_STRICT,          E_STRICT,           false]
        ];
    }
}
