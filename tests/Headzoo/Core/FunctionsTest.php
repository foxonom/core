<?php
use Headzoo\Core\Functions;

/**
 * @coversDefaultClass Headzoo\Core\Functions
 */
class FunctionsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::swapCallable
     * @dataProvider providerSwapCallable
     */
    public function testSwapCallable($optional, $callable, $default, $expected)
    {
        $this->assertEquals(
            $expected,
            Functions::swapCallable($optional, $callable, $default)
        );
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
     * @covers ::swapCallable
     */
    public function testSwapCallable_Callable()
    {
        $optional = "test";
        $callable = null;
        Functions::swapCallable($optional, $callable);
        $this->assertEquals("test", $optional);
        $this->assertNull($callable);
        
        $optional = null;
        $callable = "trim";
        Functions::swapCallable($optional, $callable);
        $this->assertNull($optional);
        $this->assertEquals("trim", $callable);
        
        $optional = "trim";
        $callable = null;
        Functions::swapCallable($optional, $callable);
        $this->assertNull($optional);
        $this->assertEquals("trim", $callable);

        $optional = "trim";
        $callable = null;
        $default  = "test";
        Functions::swapCallable($optional, $callable, $default);
        $this->assertEquals("test", $optional);
        $this->assertEquals("trim", $callable);
        
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
