<?php
use Headzoo\Core\SmartCallable;

/**
 * @coversDefaultClass Headzoo\Core\SmartCallable
 */
class SmartCallableTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var bool|string
     */
    public $completed = false;
    
    /** @var  resource */
    public $curl;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->completed = false;
    }
    
    /**
     * @covers ::factory
     */
    public function testFactory()
    {
        $wrapper = function()
        {
            // Destructor is called when $complete goes out of scope, in this
            // case is when this wrapper function returns.
            /** @noinspection PhpUnusedLocalVariableInspection */
            $sc = SmartCallable::factory(function() {
                    $this->completed = true;
                });
        };
        
        $wrapper();
        $this->assertTrue($this->completed);
        
        $this->completed = false;
        $wrapper = function()
        {
            // Destructor is called when $complete goes out of scope, in this
            // case is when this wrapper function returns.
            /** @noinspection PhpUnusedLocalVariableInspection */
            $sc = SmartCallable::factory(function($arg1, $arg2) {
                    $this->completed = "{$arg1}, {$arg2}";
                }, "Hello", "World");
        };

        $wrapper();
        $this->assertEquals("Hello, World", $this->completed);
    }

    /**
     * @covers ::factory
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage is not a valid cURL handle resource
     */
    public function testFactory_Args()
    {
        $wrapper = function()
        {
            $this->curl = curl_init();
            /** @noinspection PhpUnusedLocalVariableInspection */
            $sc = SmartCallable::factory("curl_close", $this->curl);
        };

        $wrapper();
        curl_exec($this->curl);
    }

    /**
     * @covers ::__invoke
     */
    public function test__invoke()
    {
        $complete = SmartCallable::factory(function() {
            $this->completed = true;
        });
        $complete();
        $this->assertTrue($this->completed);
    }

    /**
     * @covers ::invoke
     */
    public function testInvoke()
    {
        $complete = SmartCallable::factory(function() {
            $this->completed = true;
        });
        $complete->invoke();
        $this->assertTrue($this->completed);
    }
}
