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
 * $handler = new ErrorHandler(ENVIRONMENT);
 * 
 * $handler->setCallback("live", function($handler) {
 *      include("templates/live_error.php");
 * });
 * $handler->setCallback("dev", function($handler) {
 *      include("templates/dev_error.php");
 * });
 * 
 * $handler->handle();
 * ```
 * 
 * We pass the currently running environment to the ErrorHandler constructor, and then define
 * callbacks for the various environments our site runs under. Change the ENVIRONMENT constant,
 * and the way the error is handled changes with it.
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
        E_PARSE,
        E_CORE_ERROR,
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
     * @var array
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
     * @return int[]
     */
    public static function getDefaultCoreErrors()
    {
        return self::$default_errors;
    }
    
    /**
     * Constructor
     * 
     * @param string              $running_env The current running environment
     * @param Log\LoggerInterface $logger      Used to log errors
     */
    public function __construct($running_env = self::DEFAULT_ENVIRONMENT, Log\LoggerInterface $logger = null)
    {
        if (null === $logger) {
            $logger = new Log\NullLogger();
        }
        $this->setLogger($logger);
        $this->setRunningEnvironment($running_env);
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
     * describes the error that was handled.
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
     * @return bool
     */
    public function handle()
    {
        $is_handled = false;
        if (!$this->is_handling && !$this->last_error) {
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
     * @param string|callable   $env        Name of the environment
     * @param callable|null     $callable   The error callback
     *
     * @return $this
     */
    public function setCallback($env, callable $callable = null)
    {
        Functions::swapCallable($env, $callable, $this->running_env);
        
        $this->callbacks[$env] = $callable;
        if (empty($this->errors[$env])) {
            $this->setCoreErrors($env, self::$default_errors);
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
     * @return null
     */
    public function getCallback($env = null)
    {
        $env = $env ?: $this->running_env;
        $callable = null;
        if (isset($this->callbacks[$env])) {
            $callable = $this->callbacks[$env];
        }

        return $callable;
    }

    /**
     * Returns the default callback for errors
     *
     * The class has a default callback which handles errors by displaying an HTML5 page
     * with the error message and backtrace. This method returns a callable instace
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
    public function defaultCallback(/** @noinspection PhpUnusedParameterInspection */ $handler)
    {
        include(__DIR__ . "/templates/error.php");
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
     * $handler->setCoreErrors([E_ERROR, E_WARNING, E_DEPRECIATED]);
     * 
     * $handler->setCoreErrors("live", [E_ERROR, E_WARNING]);
     * 
     * $handler->setCoreError("dev", [E_ERROR, E_WARNING, E_NOTICE]);
     * ```
     * 
     * @param string|int[]  $env        Name of the environment
     * @param int[]         $errors     The errors to handle
     * 
     * @throws Exceptions\InvalidArgumentException When the errors array is empty
     *                                             
     * @return $this
     */
    public function setCoreErrors($env, array $errors = [])
    {
        Functions::swapArgs($env, $errors, $this->running_env);
        
        $this->errors[$env] = [];
        if (!empty($errors)) {
            foreach($errors as $error) {
                $this->errors[$env][] = Errors::toInteger($error);
            }
            if (!$this->getCallback($env)) {
                $this->setCallback($env, $this->getDefaultCallback());
            }
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
     * @return int[]
     */
    public function getCoreErrors($env = null)
    {
        $env = $env ?: $this->running_env;
        return isset($this->errors[$env]) ? $this->errors[$env] : [];
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
     * $handler->removeCoreError("dev", E_DEPRECIATED);
     * ```
     * 
     * @param  string|int   $env        Name of the environment
     * @param  string|int   $error      The error to remove
     *
     * @return bool
     */
    public function removeCoreError($env, $error = 0)
    {
        Functions::swapArgs($env, $error, $this->running_env);
        
        $is_removed = false;
        if (isset($this->errors[$env])) {
            $error = Errors::toInteger($error);
            if (E_ALL === $error) {
                $this->errors[$env] = [];
                $is_removed = true;
            } else {
                $is_removed = (bool)Arrays::remove($this->errors[$env], $error);
            }
        }
        
        return $is_removed;
    }
    
    /**
     * Sets the uncaught exceptions which will be handled
     *
     * By default every uncaught exception is handled, but specific types may be
     * specified using this method.
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
     * @param string|Exception[]  $env          Name of the environment
     * @param string[]            $exceptions   The exceptions to handle
     *
     * @throws Exceptions\InvalidArgumentException When the exceptions array is empty
     *                                             
     * @return $this
     */
    public function setUncaughtExceptions($env, array $exceptions = [])
    {
        Functions::swapArgs($env, $exceptions, $this->running_env);
        
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
     * Uses the currently running environment when none is given.
     *
     * @param  string|null $env     Name of the environment
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
     * @param  string|int     $env          Name of the environment
     * @param  Exception|null $exception
     *
     * @return bool
     */
    public function removeUncaughtException($env, $exception = null)
    {
        Functions::swapArgs($env, $exception, $this->running_env);
        
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
        Functions::swapArgs($env, $error, $this->running_env);
        
        $error = Errors::toInteger($error);
        return isset($this->errors[$env])
            && (
                in_array($error, $this->errors[$env]) ||
                in_array(E_ALL, $this->errors[$env])
            );
    }

    /**
     * Returns whether the given exception is being handled
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
        Functions::swapArgs($env, $exception, $this->running_env);
        
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
     * The callback used by ErrorHandler when it captures a core PHP error. This method
     * will call the error callback if the type of error matches one of the errors
     * being handled.
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
     * The callback used by ErrorHandler when it captures an unhandled exception. This
     * method will call the error callback if the type of exception matches the exceptions
     * being handled.
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
     * @return callable
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
     * @return callable
     */
    public function getUncaughtExceptionHandler()
    {
        return function($exception) {
            return $this->handleUncaughtException($exception);
        };
    }

    /**
     * Calls the set error callback
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