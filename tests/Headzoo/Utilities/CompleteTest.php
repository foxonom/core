<?php
use Headzoo\Utilities\Complete;

class CompleteTest
    extends PHPUnit_Framework_TestCase
{
    public $completed = false;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->completed = false;
    }
    
    /**
     * @covers Headzoo\Utilities\Complete::factory
     */
    public function testFactory()
    {
        $wrapper = function()
        {
            // Destructor is called when $complete goes out of scope, in this
            // case is when this wrapper function returns.
            /** @noinspection PhpUnusedLocalVariableInspection */
            $complete = Complete::factory(function() {
                $this->completed = true;
            });
        };
        
        $wrapper();
        $this->assertTrue($this->completed);
    }

    /**
     * @covers Headzoo\Utilities\Complete::__invoke
     */
    public function test__invoke()
    {
        $complete = Complete::factory(function() {
            $this->completed = true;
        });
        $complete();
        $this->assertTrue($this->completed);
    }

    /**
     * @covers Headzoo\Utilities\Complete::invoke
     */
    public function testInvoke()
    {
        $complete = Complete::factory(function() {
            $this->completed = true;
        });
        $complete->invoke();
        $this->assertTrue($this->completed);
    }
}
