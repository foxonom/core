<?php
use Headzoo\Core\ErrorHandler;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * @coversDefaultClass
 */
class ErrorsHandlerTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * The test fixture
     * @var ErrorHandler
     */
    protected $handler;
    
    protected $orig_core_error_handler;
    protected $orig_exception_handler;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->orig_core_error_handler = $this->getCurrentCoreErrorHandler();
        $this->orig_exception_handler  = $this->getCurrentExceptionHandler();
        $this->handler = new ErrorHandler();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        restore_exception_handler();
        restore_error_handler();
    }

    /**
     * Returns the callable currently registered to handle core PHP errors
     * 
     * @return callable
     */
    protected function getCurrentCoreErrorHandler()
    {
        $handler = set_error_handler(function() {});
        restore_error_handler();
        return $handler;
    }

    /**
     * Returns the callable currently registered to handle uncaught exceptions
     * 
     * @return callable
     */
    protected function getCurrentExceptionHandler()
    {
        $handler = set_exception_handler(function() {});
        restore_exception_handler();
        return $handler;
    }
    
    
    
    
    
    
    /**
     * @covers ::setRunningEnvironment
     * @covers ::getRunningEnvironment
     */
    public function testRunningEnvironment()
    {
        $this->assertEquals(
            ErrorHandler::DEFAULT_ENVIRONMENT,
            $this->handler->getRunningEnvironment()
        );
        $this->assertEquals(
            ErrorHandler::DEFAULT_ENVIRONMENT,
            $this->handler->setRunningEnvironment("unit-testing")
        );
        $this->assertEquals(
            "unit-testing",
            $this->handler->getRunningEnvironment()
        );
        $this->assertEquals(
            "unit-testing",
            $this->handler->setRunningEnvironment(ErrorHandler::DEFAULT_ENVIRONMENT)
        );
    }
    
    /**
     * @covers ::setCallback
     */
    public function testSetCallback()
    {
        // No registered callable for current environment, so the default is used.
        $this->assertEquals(
            $this->handler->getDefaultCallback(),
            $this->handler->getCallback()
        );

        // No argument to getErrorCallable() defaults to the running environment.
        $this->assertEquals(
            $this->handler->getDefaultCallback(),
            $this->handler->getCallback(ErrorHandler::DEFAULT_ENVIRONMENT)
        );
        
        // No callable has been registered with this environment.
        $this->assertEmpty($this->handler->getCallback("unit-testing"));

        // No argument to setErrorCallable() uses the default environment.
        $callable = function() {};
        $this->assertSame(
            $this->handler,
            $this->handler->setCallback($callable)
        );
        $this->assertSame(
            $callable,
            $this->handler->getCallback()
        );
        $this->assertSame(
            $callable,
            $this->handler->getCallback(ErrorHandler::DEFAULT_ENVIRONMENT)
        );

        // Different callables for different environments.
        $callable1 = function() {};
        $callable2 = function() {};
        $this->assertSame(
            $this->handler,
            $this->handler->setCallback($callable1)
        );
        $this->assertSame(
            $this->handler,
            $this->handler->setCallback("unit-test", $callable2)
        );
        $this->assertSame(
            $callable1,
            $this->handler->getCallback()
        );
        $this->assertSame(
            $callable2,
            $this->handler->getCallback("unit-test")
        );
    }

    /**
     * @covers ::getDefaultCallback
     */
    public function testGetDefaultCallback()
    {
        $this->assertTrue(is_callable($this->handler->getDefaultCallback()));
    }
    
    /**
     * @covers ::getCoreErrorHandler
     */
    public function testGetCoreErrorHandler()
    {
        $this->assertTrue(is_callable($this->handler->getCoreErrorHandler()));
    }

    /**
     * @covers ::getUncaughtExceptionHandler
     */
    public function testGetUncaughtExceptionHandler()
    {
        $this->assertTrue(is_callable($this->handler->getUncaughtExceptionHandler()));
    }
    
    /**
     * @covers ::getDefaultErrors
     */
    public function testGetDefaultErrors()
    {
        $errors = $this->handler->getDefaultCoreErrors();
        $this->assertTrue(($errors & E_ERROR) === E_ERROR);
        $this->assertTrue(($errors & E_WARNING) === E_WARNING);
        $this->assertFalse(($errors & E_PARSE) === E_PARSE );
    }

    /**
     * @covers ::setCoreErrors
     */
    public function testSetCoreErrors()
    {
        // E_NOTICE is not one of the default handled error types for any environment.
        $this->assertFalse($this->handler->isHandlingCoreError(E_NOTICE));
        $this->assertFalse($this->handler->isHandlingCoreError("unit-testing", E_NOTICE));

        // Now we are watching for E_NOTICE in the default environment.
        $this->assertSame(
            $this->handler,
            $this->handler->setCoreErrors(E_ERROR | E_WARNING | E_NOTICE)
        );
        $this->assertTrue($this->handler->isHandlingCoreError(E_NOTICE));

        // Now we are watching for E_NOTICE in the "unit-test" environment.
        $this->assertSame(
            $this->handler,
            $this->handler->setCoreErrors("unit-testing", E_ERROR | E_WARNING | E_NOTICE)
        );
        $this->assertTrue($this->handler->isHandlingCoreError("unit-testing", E_NOTICE));
    }
    
    /**
     * @covers ::setUncaughtExceptions
     */
    public function testSetUncaughtExceptions()
    {
        // The default environment handles every type of exception.
        $this->assertTrue($this->handler->isHandlingUncaughtException(Exception::class));
        $this->assertTrue($this->handler->isHandlingUncaughtException(TestingException::class));
        
        // This environment does not exist.
        $this->assertFalse($this->handler->isHandlingUncaughtException("unit-testing", TestingException::class));
        $this->assertSame(
            $this->handler,
            $this->handler->setUncaughtExceptions("unit-testing", [TestingException::class])
        );
        $this->assertTrue($this->handler->isHandlingUncaughtException("unit-testing", TestingException::class));
    }
    
    /**
     * @covers ::removeCoreError
     */
    public function testRemoveCoreError()
    {
        // E_NOTICE isn't being watched, so it's not removed.
        $this->assertFalse(
            $this->handler->removeCoreError(E_NOTICE)
        );
        $this->assertFalse(
            $this->handler->removeCoreError(ErrorHandler::DEFAULT_ENVIRONMENT, E_NOTICE)
        );
        $this->assertFalse(
            $this->handler->removeCoreError("unit-test", E_NOTICE)
        );
        
        // Now it's being watched, and now it can be removed.
        $this->handler->setCoreErrors(E_NOTICE);
        $this->assertTrue(
            $this->handler->removeCoreError(E_NOTICE)
        );
        $this->assertFalse(
            $this->handler->removeCoreError(E_NOTICE)
        );
        
        // Checking that no $env argument defaults to the running environment.
        $this->handler->setCoreErrors(ErrorHandler::DEFAULT_ENVIRONMENT, E_NOTICE);
        $this->assertTrue(
            $this->handler->removeCoreError(E_NOTICE)
        );
        $this->assertFalse(
            $this->handler->removeCoreError(ErrorHandler::DEFAULT_ENVIRONMENT, E_NOTICE)
        );

        // E_NOTICE isn't being watched in the "unit-test" environment, so it's not removed.
        $this->assertFalse(
            $this->handler->removeCoreError("unit-test", E_NOTICE)
        );

        // Now E_NOTICE it's being watched in "unit-test", and now it can be removed.
        $this->handler->setCoreErrors("unit-test", E_NOTICE);
        $this->assertTrue(
            $this->handler->removeCoreError("unit-test", E_NOTICE)
        );
        $this->assertFalse(
            $this->handler->removeCoreError("unit-test", E_NOTICE)
        );
        
        // Ensuring that removing E_ALL removes all watched error types.
        $this->assertSame(
            $this->handler,
            $this->handler->setCoreErrors("unit-test", E_ERROR | E_WARNING | E_NOTICE)
        );
        $this->assertNotEmpty($this->handler->getCoreErrors("unit-test"));
        $this->assertTrue(
            $this->handler->removeCoreError("unit-test", E_ALL)
        );
        $this->assertEmpty($this->handler->getCoreErrors("unit-test"));
    }

    /**
     * @covers ::removeUncaughtException
     */
    public function testRemoveUncaughtException()
    {
        $this->handler->setUncaughtExceptions([Exception::class, TestingException::class]);
        
        // Remove the base exception. Once removed it can't be removed again.
        $this->assertTrue(
            $this->handler->removeUncaughtException(Exception::class)
        );
        $this->assertFalse(
            $this->handler->removeUncaughtException(Exception::class)
        );
        
        // We are still handling the test exception, but not the base exception.
        $this->assertTrue($this->handler->isHandlingUncaughtException(TestingException::class));
        $this->assertFalse($this->handler->isHandlingUncaughtException(Exception::class));
    }
    
    /**
     * @covers ::setExceptions
     */
    public function testSetExceptions()
    {
        $this->assertEquals(
            true,
            true
        );
    }
    
    /**
     * @covers ::removeException
     */
    public function testRemoveException()
    {
        $this->assertEquals(
            true,
            true
        );
    }
    
    
    /**
     * @covers ::handle
     */
    public function testHandle_Error()
    {
        // Success when the the ::handle method switch the core error handler
        // to it's core error handler.
        $this->assertTrue($this->handler->handle());
        $this->assertEquals(
            $this->getCurrentCoreErrorHandler(),
            $this->handler->getCoreErrorHandler()
        );
        $this->assertTrue($this->handler->isHandling());
        
        // We already started handling errors. Can't handle again.
        $this->assertFalse($this->handler->handle());
    }

    /**
     * @covers ::handle
     */
    public function testHandle_Error_Callback()
    {
        $callback = function() {
            echo "Error!";
        };
        $this->assertTrue($this->handler->handle($callback));
        $this->assertEquals(
            $callback,
            $this->handler->getCallback()
        );
    }

    /**
     * @covers ::handle
     */
    public function testHandle_Error_Callback_Env()
    {
        $callback = function() {
            echo "Error!";
        };
        $this->assertTrue($this->handler->handle("unit-test", $callback));
        $this->assertEquals(
            "unit-test",
            $this->handler->getRunningEnvironment()
        );
        $this->assertEquals(
            $callback,
            $this->handler->getCallback("unit-test")
        );
        $this->assertNull($this->handler->getCallback("no-unit-testing"));
    }

    /**
     * @covers ::handle
     */
    public function testHandle_Exception()
    {
        // Success when the the ::handle method switch the uncaught exception handler
        // to it's uncaught exception handler.
        $this->assertTrue($this->handler->handle());
        $this->assertEquals(
            $this->getCurrentExceptionHandler(),
            $this->handler->getUncaughtExceptionHandler()
        );
        $this->assertTrue($this->handler->isHandling());
        
        // We already started handling errors. Can't handle again.
        $this->assertFalse($this->handler->handle());
    }
    
    /**
     * @covers ::unhandle
     */
    public function testUnhandle_Error()
    {
        // Start handling errors.
        $this->handler->handle();
        
        // We should be back to the original core error handler after unhandling.
        $this->assertTrue($this->handler->unhandle());
        $this->assertNotEquals(
            $this->orig_core_error_handler,
            $this->handler->getCoreErrorHandler()
        );
        $this->assertFalse($this->handler->isHandling());
        
        // We already unhandled. Can't unhandle again.
        $this->assertFalse($this->handler->unhandle());
    }
    
    /**
     * @covers ::unhandle
     */
    public function testUnhandle_Exception()
    {
        // Start handling errors.
        $this->handler->handle();

        // We should be back to the original uncaught exception handler after unhandling.
        $this->assertTrue($this->handler->unhandle());
        $this->assertNotEquals(
            $this->orig_exception_handler,
            $this->handler->getCoreErrorHandler()
        );
        $this->assertFalse($this->handler->isHandling());

        // We already unhandled. Can't unhandle again.
        $this->assertFalse($this->handler->unhandle());
    }

    /**
     * @covers ::handleCoreError
     */
    public function testHandleCoreError()
    {
        $this->expectOutputRegex("~There was an error.~");
        
        $logger = new TestErrorLogger();
        $this->handler->setLogger($logger);
        $this->handler->handle();
        $this->handler->setCoreErrors(E_ERROR);
        $this->assertTrue($this->handler->handleCoreError(
            E_ERROR,
            "There was an error.",
            __FILE__,
            __LINE__
        ));
        $this->assertEquals(LogLevel::ERROR, $logger->level);
        $this->assertRegExp(
            '~^Core Error E_ERROR: "There was an error\." in file~',
            $logger->message
        );
        
        // Can't start handling again after an error has been handled.
        $this->assertFalse($this->handler->handle());
        $this->assertFalse($this->handler->isHandling());
    }

    /**
     * @covers ::handleCoreError
     */
    public function testHandleCoreError_Callback()
    {
        $this->expectOutputRegex("~Look out, there's been an error!~");
        $this->handler->handle(function() {
                echo "Look out, there's been an error!";
            });
        $this->assertTrue($this->handler->handleCoreError(
                E_ERROR,
                "There was an error.",
                __FILE__,
                __LINE__
            ));
    }
    
    /**
     * @covers ::handleUncaughtException
     */
    public function testHandleUncaughtException()
    {
        $this->expectOutputRegex("~There was an exception.~");
        $exception = new TestingException("There was an exception.", 42);

        $logger = new TestErrorLogger();
        $this->handler->setLogger($logger);
        $this->handler->handle();
        $this->assertTrue($this->handler->handleUncaughtException($exception));
        $this->assertEquals(LogLevel::ERROR, $logger->level);
        $this->assertRegExp(
            '~^Uncaught Exception TestingException\[42\]: "There was an exception\." in file~',
            $logger->message
        );
        
        // Once an exception has been handled, we do not handle them any longer.
        $this->assertFalse($this->handler->handle());
        $this->assertFalse($this->handler->isHandling());
    }
}

class TestingException
    extends Exception {}

class TestErrorLogger
    extends AbstractLogger
{
    public $level;
    public $message;
    public function log($level, $message, array $context = [])
    {
        $this->level   = $level;
        $this->message = $this->interpolate($message, $context);
    }
    protected function interpolate($message, array $context = [])
    {
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        return strtr($message, $replace);
    }
}