<?php
namespace Headzoo\Core;
use Exception;
use psr\Log;
use Closure;

/**
 * Used to capture and handle core PHP errors and uncaught exceptions.
 * 
 * #### Examples
 * 
 * The most basic setup, this will handle errors which crash scripts by displaying
 * HTML5 with the error message and backtrace.
 * 
 * ```php
 * $handler = new ErrorHandler();
 * $handler->handle();
 * ```
 *
 * That's all there is too it. That may be fine during development, but in production you
 * will probably want to display
 * your own error page. (One that doesn't show sensitive information) You do that by
 * registering your own error callback instead of using the default, which will be called when an error
 * is captured. You can do whatever you want in that function: include an error page, email
 * yourself the error, etc.
 * 
 * ```php
 * $handler = new ErrorHandler();
 * $handler->setCallback(function($handler) {
 *      // The $handler parameter is the ErrorHandler instance.
 *      // The $handler->getLastError() method returns an exception which
 *      // describes the error.
 *      include("templates/error.php");
 * });
 * 
 * $handler->handle();
 * ```
 *
 * That's looking better, but you may want to handle errors differently in production and
 * development. For that situation the ErrorHandler class supports the use of "environments".
 * You tell the handler which environment -- "live", "staging", "development" -- is currently
 * running, and then defined different callbacks for each possible environment.
 * 
 * ```php
 * if (!defined("ENVIRONMENT")) {
 *      define("ENVIRONMENT", "live");
 * }
 * 
 * $handler = new ErrorHandler();
 * $handler->setCallback("live", function($handler) {
 *      include("templates/live_error.php");
 * });
 * $handler->setCallback("dev", function($handler) {
 *      include("templates/dev_error.php");
 * });
 * $handler->setRunningEnvironment(ENVIRONMENT);
 * $handler->handle();
 * ```
 *
 * We pass the currently running environment to the ErrorHandler::setRunningEnvironment() method. When
 * an error is trapped, the callback set for that environment will be called. The example could be
 * shortened a bit.
 * 
 * ```php
 * if (!defined("ENVIRONMENT")) {
 *      define("ENVIRONMENT", "live");
 * }
 *
 * $handler = new ErrorHandler();
 * $handler->setCallback("live", function($handler) {
 *      include("templates/live_error.php");
 * });
 * $handler->setCallback("dev", function($handler) {
 *      include("templates/dev_error.php");
 * });
 * $handler->handle(ENVIRONMENT);
 * ```
 * 
 * This time we pass the running environment straight to the ErrorHandler::handle() method. In fact
 * the ::handle() method can even take an error callback.
 * 
 * ```php
 * $handler = new ErrorHandler();
 * $handler->handle(function() {
 *      include("templates/error.php");
 * });
 * ```
 * 
 * That's the short, short example for using the error handler.
 * 
 * There are many more options for dealing with how errors are handled, and which errors are
 * handled. See the API documentation for more information.
 * 
 * Notes:
 * The instances of this class stop handling errors when the instance goes out of scope. Therefore the
 * instance should be created in the global scope.
 * 
 * Only the single error is handled, because the errors it handles are meant to kill execution
 * of the script. The callbacks should handle a graceful shutdown.
 */
class ErrorHandler
    extends Obj
    implements Log\LoggerAwareInterface
{
    use Log\LoggerAwareTrait;
    use FunctionsTrait;
    
    /**
     * The default runtime environment
     */
    const DEFAULT_ENVIRONMENT = "development";
    
    /**
     * The default types of errors handled for every runtime environment
     * @var int[]
     */
    private static $default_errors = [
        E_ERROR,
        E_WARNING,
        E_USER_ERROR,
        E_USER_WARNING,
        E_RECOVERABLE_ERROR
    ];

    /**
     * The default types of exceptions handled for every runtime environment
     * @var string[]
     */
    private static $default_exceptions = [
        Exception::class
    ];
    
    /**
     * The running environment
     * @var string
     */
    protected $running_env;

    /**
     * The last generated exception
     * @var
     */
    protected $last_error;

    /**
     * Whether the error handler has been activated
     * @var bool
     */
    protected $is_handling = false;
    
    /**
     * Called when an error is handled
     * @var array
     */
    protected $callbacks = [];

    /**
     * Core errors which are being handled
     * @var int[]
     */
    protected $errors = [];

    /**
     * Exceptions which are being handled
     * @var array
     */
    protected $exceptions = [];

    /**
     * The previously registered error handler
     * @var callable
     */
    protected $prev_error_handler;

    /**
     * The previously registered uncaught exception handler
     * @var callable
     */
    protected $prev_exception_handler;

    /**
     * Returns the types of uncaught exceptions which are handled by default
     *
     * @return string[]
     */
    public static function getDefaultUncaughtExceptions()
    {
        return self::$default_exceptions;
    }

    /**
     * Returns the core errors which are handled by default
     *
     * @return int
     */
    public static function getDefaultCoreErrors()
    {
        $errors = 0;
        foreach(self::$default_errors as $error) {
            $errors |= $error;
        }
        return $errors;
    }
    
    /**
     * Constructor
     * 
     * @param Log\LoggerInterface $logger Used to log errors
     */
    public function __construct(Log\LoggerInterface $logger = null)
    {
        $this->setLogger($logger ?: new Log\NullLogger());
        $this->setRunningEnvironment(self::DEFAULT_ENVIRONMENT);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->unhandle();
    }

    /**
     * Returns the last generated exception
     *
     * Generally used by the error callbacks, this method returns an exception instance which
     * describes the error that was handled. Returns null when an error has not been
     * handled.
     * 
     * @return Exception|null
     */
    public function getLastError()
    {
        return $this->last_error;
    }
    
    /**
     * Returns the current running environment
     *
     * @return string
     */
    public function getRunningEnvironment()
    {
        return $this->running_env;
    }

    /**
     * Sets the current runtime environment
     *
     * Automatically sets the default error callback for this environment if none has been
     * set already. Returns the previously set running environment.
     *
     * @param  string $running_env Name of the environment
     *
     * @return string
     */
    public function setRunningEnvironment($running_env)
    {
        $running_env = (string)$running_env;
        if (empty($running_env)) {
            $this->toss(
                "InvalidArgument",
                "The environment name cannot be empty."
            );
        }
        
        $previous = $this->running_env;
        $this->running_env = $running_env;
        if (!isset($this->callbacks[$this->running_env])) {
            $this->setCallback(
                $this->running_env,
                $this->getDefaultCallback()
            );
        }

        return $previous;
    }
    
    /**
     * Starts handling errors
     *
     * Returns true when errors are now being handled, or false when errors were already
     * being handled. Possibly because ::handle() had already been called, or an error has
     * already been handled.
     * 
     * Examples:
     * ```
     * $handler = new ErrorHandler();
     * $handler->handle();
     * 
     * $handler = new ErrorHandler();
     * $handler->setCallback(function() { echo "Look out!"; });
     * $handler->handle();
     * 
     * $handler = new ErrorHandler();
     * $handler->setRunningEnvironment("live");
     * $handler->setCallback("live", function() { echo "Look out!"; });
     * $handler->handle();
     * 
     * $handler = new ErrorHandler();
     * $handler->handle("live", function() { echo "Look out!"; });
     * ```
     * 
     * @param  string   $running_env The running environment
     * @param  callable $callback    The error callback
     * @return bool
     */
    public function handle($running_env = self::DEFAULT_ENVIRONMENT, callable $callback = null)
    {
        $is_handled = false;
        if (!$this->is_handling && !$this->last_error) {
            $this->swapCallable($running_env, $callback, $this->running_env, false);
            $this->setRunningEnvironment($running_env);
            if ($callback) {
                $this->setCallback($callback);
            }
            
            $this->prev_exception_handler = set_exception_handler($this->getUncaughtExceptionHandler());
            $this->prev_error_handler     = set_error_handler($this->getCoreErrorHandler());
            register_shutdown_function(function() {
                    if ($error = error_get_last()) {
                        $this->handleCoreError(
                            $error["type"],
                            $error["message"],
                            $error["file"],
                            $error["line"]
                        );
                    }
                });
            
            $this->is_handling = true;
            $is_handled = true;
        }

        return $is_handled;
    }

    /**
     * Stop handling errors
     *
     * Restores the original uncaught exception handler, core error handler, and stops
     * handling errors.
     * 
     * Returns true when the original error handling state has been restored, or false when
     * the class was not handling errors. Possibly because ::handle() was never called, or
     * ::unhandle() was already called, or an error has already been handled.
     * 
     * @return bool
     */
    public function unhandle()
    {
        $is_unhandled = false;
        if ($this->is_handling && !$this->last_error) {
            if ($this->prev_exception_handler) {
                set_exception_handler($this->prev_exception_handler);
                $this->prev_exception_handler = null;
            }
            if ($this->prev_error_handler) {
                set_error_handler($this->prev_error_handler);
                $this->prev_error_handler = null;
            }
            $this->is_handling = false;
            $is_unhandled      = true;
        }

        return $is_unhandled;
    }

    /**
     * Returns whether errors are being handled
     * 
     * @return bool
     */
    public function isHandling()
    {
        return $this->is_handling;
    }

    /**
     * Sets the callback that will be called when an error is handled
     * 
     * Uses the currently running environment when none is given.
     * 
     * Examples:
     * ```php
     * $handler->setCallback(function($handler) {
     *      // The $handler parameter is the ErrorHandler instance.
     *      // The $handler->getLastError() method returns an exception which
     *      // describes the error.
     *      include("templates/error.php");
     * });
     * 
     * $handler->setCallback("live", function($handler) {
     *      include("templates/error_live.php");
     * });
     * 
     * $handler->setCallback("dev", function($handler) {
     *      include("templates/error_dev.php");
     * });
     * ```
     * 
     * @param string|callable $env      Name of the environment
     * @param callable|null   $callable The error callback
     *
     * @return $this
     */
    public function setCallback($env, callable $callable = null)
    {
        $this->swapCallable($env, $callable, $this->running_env);
        
        $this->callbacks[$env] = $callable;
        if (empty($this->errors[$env])) {
            $this->setCoreErrors($env, self::getDefaultCoreErrors());
        }
        if (empty($this->exceptions[$env])) {
            $this->setUncaughtExceptions($env, self::$default_exceptions);
        }
        
        return $this;
    }

    /**
     * Returns the callback that will be called when an error is handled
     * 
     * Uses the currently running environment when none is given.
     * 
     * @param  string|null $env Name of the environment
     * 
     * @return callable
     */
    public function getCallback($env = null)
    {
        $env = $env ?: $this->running_env;
        return isset($this->callbacks[$env]) ? $this->callbacks[$env] : null;
    }

    /**
     * Returns the default callback for errors
     *
     * The class has a default callback which handles errors by displaying an HTML5 page
     * with the error message and backtrace. This method returns a callable instance
     * of that default callback.
     * 
     * @return Closure
     */
    public function getDefaultCallback()
    {
        return function($handler) {
            return $this->defaultCallback($handler);
        };
    }

    /**
     * The default error callback
     * 
     * This is the default error callback. It displays a web page with error information.
     * This should not be used in production. Ever.
     *
     * @param  ErrorHandler $handler The object that handled the error
     * @return mixed
     */
    public function defaultCallback($handler)
    {
        $exception = $handler->getLastError();
        if (php_sapi_name() != "cli") {
            $message   = htmlspecialchars($exception->getMessage());
            $trace     = nl2br(htmlspecialchars($exception->getTraceAsString()));
            echo "<!DOCTYPE html><html><head><title>Error</title></head><body>",
                "<h1>{$message}</h1><p>{$trace}</p></body><html>";
        } else {
            echo $exception->getMessage() . PHP_EOL,
                "-----------" . PHP_EOL,
                $exception->getTraceAsString() . PHP_EOL;
        }
    }
    
    /**
     * Sets the core errors which will be handled
     * 
     * By default only a few fatal errors are handled, but you can specify exactly which
     * errors to handle with this method.
     * 
     * Uses the currently running environment when none is given.
     * 
     * Examples:
     * ```php
     * $handler->setCoreErrors(E_ERROR | E_WARNING | E_DEPRECIATED);
     * 
     * $handler->setCoreErrors("live", E_ERROR | E_WARNING);
     * 
     * $handler->setCoreError("dev", E_ERROR | E_WARNING | E_NOTICE);
     * ```
     * 
     * @param string|int $env    Name of the environment
     * @param int        $errors The errors to handle
     *                                             
     * @return $this
     */
    public function setCoreErrors($env, $errors = 0)
    {
        $this->swapArgs($env, $errors, $this->running_env, false);
        
        $this->errors[$env] = $errors;
        if (!$this->getCallback($env)) {
            $this->setCallback($env, $this->getDefaultCallback());
        }
        
        return $this;
    }

    /**
     * Returns the core errors which are being handled
     * 
     * Uses the currently running environment when none is given.
     * 
     * @param  string|null $env     Name of the environment
     * 
     * @return int
     */
    public function getCoreErrors($env = null)
    {
        $env = $env ?: $this->running_env;
        return isset($this->errors[$env]) ? $this->errors[$env] : 0;
    }
    
    /**
     * Stops handling a core error
     *
     * Uses the currently running environment when none is given. Passing E_ALL removes all error handling
     * for the given environment.
     * 
     * Examples:
     * ```php
     * $handler->removeCoreError(E_NOTICE);
     * 
     * $handler->removeCoreError("live", E_NOTICE);
     * 
     * $handler->removeCoreError("dev", E_NOTICE | E_DEPRECIATED);
     * ```
     * 
     * @param  string|int   $env        Name of the environment
     * @param  string|int   $error      The error to remove
     *
     * @return bool
     */
    public function removeCoreError($env, $error = 0)
    {
        $this->swapArgs($env, $error, $this->running_env);
        
        $is_removed = false;
        if (isset($this->errors[$env])) {
            $orig = $this->errors[$env];
            $this->errors[$env] = ($this->errors[$env] & ~ $error);
            $is_removed = $this->errors[$env] !== $orig;
        }
        
        return $is_removed;
    }
    
    /**
     * Sets the uncaught exceptions which will be handled
     *
     * By default every uncaught exception is handled, but specific types may be
     * specified using this method. The $exceptions argument may be an array of class
     * names, or array of objects.
     * 
     * Uncaught exceptions will not be handled when this method is given an empty
     * array.
     * 
     * Uses the currently running environment when none is given.
     *
     * Examples:
     * ```php
     * $handler->setUncaughtExceptions([RuntimeException::class, LogicException::class]);
     * 
     * $handler->setUncaughtException("live", [RuntimeException::class, LogicException::class]);
     * 
     * $handler->setUncaughtException("dev", [InvalidArgumentException::class, LogicException::class]);
     * ```
     * 
     * @param string|string[]|Exception[] $env        Name of the environment
     * @param string[]|Exception[]        $exceptions The exceptions to handle
     *                                             
     * @return $this
     */
    public function setUncaughtExceptions($env, array $exceptions = [])
    {
        $this->swapArgs($env, $exceptions, $this->running_env, false);
        
        $this->exceptions[$env] = [];
        if (!empty($exceptions)) {
            foreach($exceptions as $exception) {
                $this->exceptions[$env][] = Objects::getFullName($exception);
            }
            if (!$this->getCallback($env)) {
                $this->setCallback($env, $this->getDefaultCallback());
            }
        }

        return $this;
    }

    /**
     * Returns the types of uncaught exceptions which are being handled
     *
     * Returns an array of class names representing the types of uncaught exceptions the
     * class is handling.
     * 
     * Uses the currently running environment when none is given.
     *
     * @param  string|null $env Name of the environment
     *
     * @return string[]
     */
    public function getUncaughtExceptions($env = null)
    {
        $env = $env ?: $this->running_env;
        return isset($this->exceptions[$env]) ? $this->exceptions[$env] : [];
    }

    /**
     * Stops handling an uncaught exception
     * 
     * Tells the class to stop handling the given type of exception, which may be either
     * a class name, or object. Returns true if the exception type has been successfully
     * unhandled. A false return value means the class wasn't handling the given
     * exception.
     * 
     * Uses the currently running environment when none is given.
     * 
     * Examples:
     * ```php
     * $handler->removeUncaughtException(LogicError::class);
     * 
     * $handler->removeUncaughtException("live", LogicError::class);
     * 
     * $handler->removeUncaughtException("dev", InvalidArgumentException::class);
     * ```
     *
     * @param  string|Exception|string  $env       Name of the environment
     * @param  Exception|string|null    $exception The exception to check
     *
     * @return bool
     */
    public function removeUncaughtException($env, $exception = null)
    {
        $this->swapArgs($env, $exception, $this->running_env);
        
        $is_removed = false;
        if (isset($this->exceptions[$env])) {
            $exception  = Objects::getFullName($exception);
            $is_removed = (bool)Arrays::remove($this->exceptions[$env], $exception);
        }
        
        return $is_removed;
    }
    
    /**
     * Returns whether errors of the given type are being handled
     * 
     * Returns true when the given error -- one of the E_ERROR constants -- is one of
     * the errors being handled. Otherwise false is returned.
     * 
     * Uses the currently running environment when none is given.
     * 
     * Examples:
     * ```php
     * $is_handled = $handler->isHandlingCoreError(E_WARNING);
     * 
     * $is_handled = $handler->isHandlingCoreError("live", E_WARNING);
     * ```
     * 
     * @param  string|int $env   The environment to check
     * @param  string|int $error The core error to check
     *
     * @return bool
     */
    public function isHandlingCoreError($env, $error = 0)
    {
        $this->swapArgs($env, $error, $this->running_env);
        return isset($this->errors[$env]) && (($this->errors[$env] & $error) === $error);
    }

    /**
     * Returns whether the given exception is being handled
     *
     * Returns true when the given exception is one of the exceptions being handled for
     * the given running environment. Otherwise false is returned. The $exception
     * argument can be a class name or object.
     * 
     * Uses the currently running environment when none is given.
     * 
     * Examples:
     * ```php
     * $is_handled = $handler->isHandlingUncaughtException(LogicException::class);
     * 
     * $is_handled = $handler->isHandlingUncaughtException("live", LogicException::class);
     * ```
     *
     * @param  string|int             $env       The environment to check
     * @param  string|Exception|null  $exception The exception to check
     *
     * @return bool
     */
    public function isHandlingUncaughtException($env, $exception = null)
    {
        $this->swapArgs($env, $exception, $this->running_env);
        
        $is_handling = false;
        if (isset($this->exceptions[$env])) {
            if (is_object($exception)) {
                $exception = Objects::getFullName($exception);
            }
            foreach($this->exceptions[$env] as $e) {
                if (is_subclass_of($exception, $e) || $exception == $e) {
                    $is_handling = true;
                    break;
                }
            }
        }
        
        return $is_handling;
    }
    
    /**
     * Handles core errors
     * 
     * Called by PHP when an error occures. This method determines whether the error is one
     * of the errors being handled. If it is, the the error will be captured, and the
     * error callback is called.
     * 
     * @param int    $type      The level of the error raised
     * @param string $message   The error message
     * @param string $file      The filename that the error was raised in
     * @param int    $line      The line number the error was raised at
     *                          
     * @return bool
     */
    public function handleCoreError($type, $message, $file, $line)
    {
        $is_handled = false;
        if ($this->is_handling && $this->isHandlingCoreError($type)) {
            $exception = new Exceptions\PHPErrorException(
                $message,
                $type,
                $file,
                $line
            );
            $is_handled = $this->triggerError($exception, "Core Error");
        }
        
        return $is_handled;
    }

    /**
     * Handles uncaught exceptions
     *
     * Called by PHP when an exception goes uncaught. This method determines whether the
     * exception is one of the exceptions being handled. If it is, the the exception will be
     * captured, and the error callback is called.
     * 
     * @param Exception $exception The exception to handle
     *
     * @return bool
     */
    public function handleUncaughtException(Exception $exception)
    {
        $is_handled = false;
        if ($this->is_handling && $this->isHandlingUncaughtException($exception)) {
            $is_handled = $this->triggerError($exception, "Uncaught Exception");
        }
        
        return $is_handled;
    }

    /**
     * Returns the callback being used to handle core errors
     *
     * This method is primarily used internally when setting up error callbacks, but
     * is left public for testing purposes. It essentially returns a reference to
     * the ::handleCoreError() method.
     * 
     * The returned closure has the following signature:
     * ```php
     * function(int $type, string $message, string $file, int $line) {}
     * ```
     * 
     * @return Closure
     */
    public function getCoreErrorHandler()
    {
        return function($type, $message, $file, $line) {
            return $this->handleCoreError($type, $message, $file, $line);
        };
    }

    /**
     * Returns the callback being used to handle uncaught exceptions.
     *
     * This method is primarily used internally when setting up error callbacks, but
     * is left public for testing purposes. It essentially returns a reference to
     * the ::handleUncaughtException() method.
     * 
     * The returned closure has the following signature:
     * ```php
     * function(Exception $exception) {}
     * ```
     * 
     * @return Closure
     */
    public function getUncaughtExceptionHandler()
    {
        return function(Exception $exception) {
            return $this->handleUncaughtException($exception);
        };
    }

    /**
     * Calls the error callback
     * 
     * Called by ::handleCoreError() and ::handleUncaughtException() when the type of error
     * captured matches an error being handled. This method calls the error callback, and
     * effectively shuts down the handler.
     * 
     * @param Exception $exception  The error
     * @param string    $label      Label for the reason the error is being triggered
     *
     * @return bool
     */
    protected function triggerError(Exception $exception, $label)
    {
        $this->unhandle();
        $this->last_error = $exception;
        
        if ($exception instanceof Exceptions\PHPErrorException) {
            $type = Errors::toString($exception->getCode());
        } else {
            $code = $exception->getCode();
            $type = get_class($exception);
            $type = "{$type}[{$code}]";
        }
        $this->logger->error(
            '{label} {type}: "{message}" in file {file}[{line}].',
            [
                "label"   => $label,
                "type"    => $type,
                "message" => $exception->getMessage(),
                "file"    => $exception->getFile(),
                "line"    => $exception->getLine()
            ]
        );
        
        try {
            call_user_func($this->getCallback(), $this);
        } catch (Exception $e) {}
        
        return true;
    }
} 