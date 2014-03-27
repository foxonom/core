<?php
namespace Headzoo\Utilities\Tests;
use Headzoo\Utilities\Obj;

/**
 * @coversDefaultClass Headzoo\Utilities\Obj
 */
class ObjTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * The test fixture
     * @var ObjTestClass
     */
    protected $core;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->core = new ObjTestClass();
    }

    /**
     * @covers ::getClassName
     */
    public function testClassName()
    {
        $this->assertEquals(
            __NAMESPACE__ . '\ObjTestClass',
            $this->core->getClassName()
        );
    }

    /**
     * @covers ::getNamespaceName
     */
    public function testNamespaceName()
    {
        $this->assertEquals(
            __NAMESPACE__,
            $this->core->getNamespaceName()
        );
    }

    /**
     * @covers ::toss
     */
    public function testToss()
    {
        $e = null;
        try {
            $this->core->tossit("CoreTestException", "There was an error.");
        } catch (Exceptions\CoreTestException $e) {}
        
        $this->assertInstanceOf(
            'Headzoo\Utilities\Tests\Exceptions\CoreTestException',
            $e
        );
        $this->assertEquals("There was an error.", $e->getMessage());
        $this->assertEquals(0, $e->getCode());

        $e = null;
        try {
            $this->core->tossit("CoreTestException", "There was another error.", 42);
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertInstanceOf(
            'Headzoo\Utilities\Tests\Exceptions\CoreTestException',
            $e
        );
        $this->assertEquals("There was another error.", $e->getMessage());
        $this->assertEquals(42, $e->getCode());
    }

    /**
     * @covers ::toss
     */
    public function testToss_Interpolation()
    {
        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException", 
                "There was a {0} error.",
                "database"
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals("There was a database error.", $e->getMessage());
        $this->assertEquals(0, $e->getCode());

        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException",
                "There was a {0} error.",
                42,
                "database"
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals("There was a database error.", $e->getMessage());
        $this->assertEquals(42, $e->getCode());

        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException",
                "There was a {0} error at {1}.",
                "database",
                "testing"
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals("There was a database error at testing.", $e->getMessage());
        $this->assertEquals(0, $e->getCode());

        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException",
                "There was a {1} error at {0}.",
                "testing",
                "database"
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals("There was a database error at testing.", $e->getMessage());
        $this->assertEquals(0, $e->getCode());

        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException",
                "There was a {0} error at {0}.",
                "database",
                "testing"
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals("There was a database error at database.", $e->getMessage());
        $this->assertEquals(0, $e->getCode());
    }

    /**
     * @covers ::toss
     */
    public function testToss_Interpolation_Defaults()
    {
        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException",
                "The class {me} threw an exception."
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals(
            "The class Headzoo\\Utilities\\Tests\\ObjTestClass threw an exception.", 
            $e->getMessage()
        );
        $this->assertEquals(0, $e->getCode());

        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException",
                "The class {me} threw a {0} exception.",
                "serious"
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals(
            "The class Headzoo\\Utilities\\Tests\\ObjTestClass threw a serious exception.",
            $e->getMessage()
        );
        $this->assertEquals(0, $e->getCode());

        $e = null;
        try {
            $this->core->tossit(
                "CoreTestException",
                "The class {me} threw a {0} exception.",
                42,
                "serious"
            );
        } catch (Exceptions\CoreTestException $e) {}

        $this->assertEquals(
            "The class Headzoo\\Utilities\\Tests\\ObjTestClass threw a serious exception.",
            $e->getMessage()
        );
        $this->assertEquals(42, $e->getCode());
    }
}


class ObjTestClass
    extends Obj
{
    public function tossit()
    {
        return call_user_func_array([$this, "toss"], func_get_args());
    }
}