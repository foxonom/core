<?php
use Headzoo\Core\ConfigurableCallable;
use Headzoo\Core\Exceptions\PHPErrorException;
use Headzoo\Core\Exceptions\BadMethodCallException;
use Headzoo\Core\Strings;

class ConfigurableCallableTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * The test fixture
     * @var ConfigurableCallable
     */
    protected $fixture;
    
    /** @var int */
    public static $tries = 0;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        self::$tries = 0;
        $this->fixture = ConfigurableCallable::factory(function() {
            return func_get_args();
        });
    }
    
    /**
     * @covers ::invoke
     * @dataProvider providerInvoke
     */
    public function testInvoke($func, $arg, $method, $returns, $tries, $exception)
    {
        $fixture = new ConfigurableCallable($func);
        if ($method) {
            if (null !== $arg) {
                $fixture->$method($arg);
            } else {
                $fixture->$method();
            }
        }
        
        $thrown   = null;
        $returned = null;
        try {
            $returned = $fixture();
        } catch (Exception $thrown) {}
        $this->assertEquals($returns, $returned);
        $this->assertEquals($tries, self::$tries);
        if ($exception) {
            $this->assertInstanceOf($exception, $thrown);
        }
    }
    
    /**
     * @covers ::invoke
     * @dataProvider providerInvoke_Invalid
     */
    public function testInvoke_Invalid($method, $arg, $exception)
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }
        $fixture = new ConfigurableCallable(function() {});
        if (null !== $arg) {
            $fixture->$method($arg);
        } else {
            $fixture->$method();
        }
        $this->assertTrue(true);
    }
    
    /**
     * @covers ::setFilter
     */
    public function testSetFilter()
    {
        $this->fixture->setFilter(function($args) {
                return strtoupper($args[0]) . " " . strtoupper($args[1]);
            });
        $fixture = $this->fixture;
        $this->assertEquals(
            "HELLO WORLD",
            $fixture("hello", "world")
        );
    }

    /**
     * Data provider for testInvoke
     *
     * @return array
     */
    public function providerInvoke()
    {
        $max = ConfigurableCallable::DEFAULT_MAX_RETRIES;
        $strings = new Strings();
        $func_ex = function() {
            self::$tries++;
            throw new LogicException("Error");
        };
        $func_ex2 = function() {
            self::$tries++;
            throw new RuntimeException("Error");
        };
        $func_er = function() {
            self::$tries++;
            trigger_error("Error!", E_USER_ERROR);
        };
        $func_true = function() {
            self::$tries++;
            return true;
        };
        $func_false = function() {
            self::$tries++;
            return false;
        };
        $func_null = function() {
            self::$tries++;  
            return null;
        };
        $func_inst = function() use($strings) {
            self::$tries++;
            return $strings;
        };
        $func_42 = function() {
            self::$tries++;
            return 42;
        };
        
        return [
            // $func,       $arg,                   $method,                        $returns,   $tries,     $exception
            [$func_ex,      null,                   null,                           null,       1,          LogicException::class],
            [$func_ex,      null,                   "returnOnException",            null,       1,          null],
            [$func_ex,      LogicException::class,  "retryOnException",             null,       $max,       LogicException::class],
            [$func_ex,      LogicException::class,  "throwOnException",             null,       1,          LogicException::class],
            [$func_ex2,     LogicException::class,  "retryOnException",             null,       1,          RuntimeException::class],
            [$func_ex2,     LogicException::class,  "throwOnException",             null,       1,          RuntimeException::class],

            [$func_er,      null,                   null,                           null,       1,          PHPErrorException::class],
            [$func_er,      null,                   "returnOnError",                null,       1,          null],
            [$func_er,      null,                   "retryOnError",                 null,       $max,       PHPErrorException::class],
            [$func_er,      null,                   "throwOnError",                 null,       1,          PHPErrorException::class],
            
            [$func_true,    null,                   null,                           true,       1,          null],
            [$func_true,    null,                   "retryOnTrue",                  true,       $max,       null],
            [$func_true,    null,                   "returnOnTrue",                 true,       1,          null],

            [$func_false,   null,                   null,                           false,      1,          null],
            [$func_false,   null,                   "retryOnFalse",                 false,      $max,       null],
            [$func_false,   null,                   "returnOnFalse",                false,      1,          null],

            [$func_null,    null,                   null,                           null,       1,          null],
            [$func_null,    null,                   "retryOnNull",                  null,       $max,       null],
            [$func_null,    null,                   "returnOnNull",                 null,       1,          null],
            [$func_true,    null,                   "returnOnNull",                 true,       1,          null],
            
            [$func_inst,    Strings::class,         "retryOnInstanceOf",            $strings,   $max,       null],
            [$func_inst,    Strings::class,         "returnOnInstanceOf",           $strings,   1,          null],
            [$func_true,    Strings::class,         "retryOnInstanceOf",            true,       1,          null],
            
            [$func_42,      null,                   null,                           42,         1,          null],
            [$func_42,      42,                     "retryOnEquals",                42,         $max,       null],
            [$func_42,      42,                     "returnOnEquals",               42,         1,          null],

            [$func_42,      42,                     "retryOnNotEquals",             42,         1,          null],
            [$func_42,      43,                     "retryOnNotEquals",             42,         $max,       null],

            [$func_42,      42,                     "retryOnLessThan",              42,         1,          null],
            [$func_42,      43,                     "retryOnLessThan",              42,         $max,       null],
            [$func_42,      43,                     "returnOnLessThan",             42,         1,          null],

            [$func_42,      41,                     "retryOnLessThanOrEquals",      42,         1,          null],
            [$func_42,      42,                     "retryOnLessThanOrEquals",      42,         $max,       null],
            [$func_42,      43,                     "retryOnLessThanOrEquals",      42,         $max,       null],
            [$func_42,      43,                     "returnOnLessThanOrEquals",     42,         1,          null],

            [$func_42,      42,                     "returnOnGreaterThan",          42,         1,          null],
            [$func_42,      41,                     "retryOnGreaterThan",           42,         $max,       null],
            [$func_42,      41,                     "returnOnGreaterThan",          42,         1,          null],

            [$func_42,      43,                     "returnOnGreaterThanOrEquals",  42,         1,          null],
            [$func_42,      41,                     "retryOnGreaterThanOrEquals",   42,         $max,       null],
            [$func_42,      42,                     "retryOnGreaterThanOrEquals",   42,         $max,       null],
        ];
    }

    /**
     * Data provider for testInvoke_Invalid
     *
     * @return array
     */
    public function providerInvoke_Invalid()
    {
        return [
            // $method,             $arg,               $exception
            ["retryOnInstanceOf",   null,               BadMethodCallException::class],
            ["returnOnInstanceOf",  null,               BadMethodCallException::class],
            ["retryOnInstanceOf",   Strings::class,     null],

            ["retryOnEquals",       null,               BadMethodCallException::class],
            ["returnOnNotEquals",   null,               BadMethodCallException::class],
            ["returnOnNotEquals",   42,                 null],

            ["retryOnException",    null,               null],
            ["retryOnException",    Exception::class,   null]
        ];
    }
}
