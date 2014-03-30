<?php
use Headzoo\Core\Functions;

/**
 * @coversDefaultClass Headzoo\Core\Functions
 */
class FunctionsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::swapArgs
     */
    public function testSwapArgs()
    {
        $op = "live";
        $swap = null;
        $this->assertTrue(
            Functions::swapArgs($op, $swap)
        );
        $this->assertNull($op);
        $this->assertEquals("live", $swap);
        
        $op = "live";
        $swap = "dev";
        $this->assertFalse(
            Functions::swapArgs($op, $swap)
        );
        $this->assertEquals("live", $op);
        $this->assertEquals("dev", $swap);
    }
    
    /**
     * @covers ::swapCallable
     * @dataProvider providerSwapCallable
     */
    public function testSwapCallable($optional, $callable, $default, $expected)
    {
        $this->assertEquals(
            $expected,
            Functions::swapCallable($optional, $callable, $default, false)
        );
    }
    
    /**
     * @covers ::swapCallable
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testSwapCallable_Invalid()
    {
        $optional = null;
        $callable = null;
        Functions::swapCallable($optional, $callable);
    }

    /**
     * @covers ::validateRequired
     */
    public function testValidateRequired()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal",
            "age"    => 38,
            "gender" => "male"
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];

        Functions::validateRequired($values, $required);
    }

    /**
     * @covers ::validateRequired
     */
    public function testValidateRequired_Empty()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal",
            "age"    => null,
            "gender" => "male"
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];

        Functions::validateRequired($values, $required, true);
    }

    /**
     * @covers ::validateRequired
     * @expectedException Headzoo\Core\Exceptions\ValidationFailedException
     */
    public function testValidateRequired_Invalid()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal"
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];

        Functions::validateRequired($values, $required);
    }

    /**
     * @covers ::validateRequired
     * @expectedException Headzoo\Core\Exceptions\ValidationFailedException
     */
    public function testValidateRequired_Invalid_Empty()
    {
        $values = [
            "name"   => "headzoo",
            "job"    => "circus animal",
            "gender" => "male",
            "age"    => null
        ];
        $required = [
            "name",
            "age",
            "gender"
        ];

        Functions::validateRequired($values, $required);
    }

    /**
     * Data provider for testSwapCallable
     * 
     * @return array
     */
    public function providerSwapCallable()
    {
        return [
            ["test", null,   null, false],
            ["test", "trim", null, false],
            [null,   "trim", null, false],
            ["trim", null,   null, true]
        ];
    }
}
