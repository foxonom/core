<?php
use Headzoo\Core\FunctionsTrait;

/**
 * @coversDefaultClass Headzoo\Core\FunctionsTrait
 */
class FunctionsTraitTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * The test fixture
     * @var FunctionsTestClass
     */
    protected $fixture;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->fixture = new FunctionsTestClass();
    }
    
    /**
     * @covers ::swapArgs
     */
    public function testSwapArgs()
    {
        $op = "live";
        $swap = null;
        $this->assertTrue(
            $this->fixture->swapArgsTest($op, $swap)
        );
        $this->assertNull($op);
        $this->assertEquals("live", $swap);
        
        $op = "live";
        $swap = "dev";
        $this->assertFalse(
            $this->fixture->swapArgsTest($op, $swap)
        );
        $this->assertEquals("live", $op);
        $this->assertEquals("dev", $swap);
    }

    /**
     * @covers ::swapArgs
     * @expectedException Headzoo\Core\Exceptions\InvalidArgumentException
     */
    public function testSwapArgs_Invalid()
    {
        $op = null;
        $swap = null;
        $this->fixture->swapArgsTest($op, $swap, null, "swap");
    }
    
    /**
     * @covers ::swapCallable
     * @dataProvider providerSwapCallable
     */
    public function testSwapCallable($optional, $callable, $default, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->fixture->swapCallableTest($optional, $callable, $default, false)
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
        $this->fixture->swapCallableTest($optional, $callable);
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

        $this->fixture->validateRequiredTest($values, $required);
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

        $this->fixture->validateRequiredTest($values, $required, true);
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

        $this->fixture->validateRequiredTest($values, $required);
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

        $this->fixture->validateRequiredTest($values, $required);
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

class FunctionsTestClass
{
    use FunctionsTrait;
    
    public function swapArgsTest(&$optional, &$swap, $default = null, $swap_required = true)
    {
        return $this->swapArgs($optional, $swap, $default, $swap_required);
    }
    
    public function swapCallableTest(&$optional, &$callable, $default = null, $callable_required = true)
    {
        return $this->swapCallable($optional, $callable, $default, $callable_required);
    }
    
    public function validateRequiredTest(array $values, array $required, $allow_empty = false)
    {
        return $this->validateRequired($values, $required, $allow_empty);
    }
}