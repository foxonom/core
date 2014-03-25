<?php
use Headzoo\Utilities\Functions;

class FunctionsTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Headzoo\Utilities\Functions::swapCallable
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
     * @covers Headzoo\Utilities\Functions::swapCallable
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
