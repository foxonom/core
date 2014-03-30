<?php
namespace Headzoo\Core;
use Exception;
use psr\Log;

/**
 * Error handler.
 * 
 * The object instances of this class stop handling errors when the instance goes out of scope. Therefore the
 * instance should be created in the global scope.
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
     * The default error handler
     */
    const DEFAULT_CALLBACK = "defaultCallback";

    /**
     * The name of a method in this class which handles uncaught exceptions.
     */
    const HANDLER_UNCAUGHT_EXCEPTIONS = "handleUncaughtException";

    /**
     * The name of a method in this class method which handles core errors.
     */
    const HANDLER_CORE_ERRORS = "handleCoreError";
    
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
     * @param string              $running_env The current runtime environment
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
     * @return Exception|null
     */
    public function getLastError()
    {
        return $this->last_error;
    }
    
    /**
     * Returns the current runtime environment
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
     * Automatically sets the default error callable for this environment if none has been
     * set already.
     *
     * Returns the previously running environment.
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
        $handled = false;
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
            $handled = true;
        }

        return $handled;
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
     * Sets the callback that will be called when an error is handled in the given environment
     * 
     * Defaults to the currently running environment.
     * 
     * @param string|callable   $env
     * @param callable|null     $callable
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
     * Returns the callback that will be called when an error is handled in the given environment
     * 
     * Defaults to the currently running environment.
     * 
     * @param  string|null $env
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
     * @return callable
     */
    public function getDefaultCallback()
    {
        return [$this, self::DEFAULT_CALLBACK];
    }

    /**
     * The default error callback
     * 
     * This should not be used in production. Ever.
     *
     * @param  ErrorHandler $handler The object that handled the error
     */
    public function defaultCallback(/** @noinspection PhpUnusedParameterInspection */ $handler)
    {
        include(__DIR__ . "/templates/error.php");
    }
    
    /**
     * Sets the core PHP errors which will be handled
     * 
     * Defaults to the currently running environment.
     * 
     * @param string|int[]  $env
     * @param int[]         $errors
     * 
     * @throws Exceptions\InvalidArgumentException When the errors array is empty
     *                                             
     * @return $this
     */
    public function setCoreErrors($env, array $errors = [])
    {
        if (func_num_args() == 1) {
            $errors = $env;
            $env    = $this->running_env;
        }
        if (empty($errors)) {
            $this->toss(
                "InvalidArgument",
                "At least one core error must be set."
            );    
        }
        
        $this->errors[$env] = [];
        foreach($errors as $error) {
            $error = Errors::toInteger($error);
            $this->errors[$env][] = $error;
            if (!$this->getCallback($env)) {
                $this->setCallback($env, $this->getDefaultCallback());
            }
        }
        
        return $this;
    }

    /**
     * Returns the core errors which are being handled
     * 
     * Defaults to the currently running environment.
     * 
     * @param  string|null $env
     * 
     * @return int[]
     */
    public function getCoreErrors($env = null)
    {
        $env    = $env ?: $this->running_env;
        $errors = [];
        if (isset($this->errors[$env])) {
            $errors = $this->errors[$env];
        }
        
        return $errors;
    }
    
    /**
     * Stops handling a core error
     * 
     * Defaults to the currently running environment.
     * 
     * Note: Passing E_ALL removes all error handling for the given environment.
     * Note: The registered error callable is removed once every type has been removed for the given
     * environment.
     * 
     * @param  string|int   $env
     * @param  string|int   $error
     *
     * @return bool
     */
    public function removeCoreError($env, $error = 0)
    {
        if (func_num_args() == 1) {
            $error = $env;
            $env   = $this->running_env;
        }
        $error = Errors::toInteger($error);
        
        $is_removed = false;  
        if (isset($this->errors[$env])) {
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
     * Sets the exceptions which will be handled
     *
     * Defaults to the currently running environment.
     *
     * @param string|Exception[]  $env
     * @param string[]            $exceptions
     *
     * @throws Exceptions\InvalidArgumentException When the exceptions array is empty
     *                                             
     * @return $this
     */
    public function setUncaughtExceptions($env, array $exceptions = [])
    {
        if (func_num_args() == 1) {
            $exceptions = $env;
            $env        = $this->running_env;
        }
        if (empty($exceptions)) {
            $this->toss(
                "InvalidArgument",
                "At least one uncaught exception must be set."
            );
        }
        
        $this->exceptions[$env] = [];
        foreach($exceptions as $exception) {
            $this->exceptions[$env][] = Objects::getFullName($exception);
            if (!$this->getCallback($env)) {
                $this->setCallback($env, $this->getDefaultCallback());
            }
        }

        return $this;
    }

    /**
     * Returns the types of uncaught exceptions which are being handled
     *
     * Defaults to the currently running environment.
     *
     * @param  string|null $env
     *
     * @return string[]
     */
    public function getUncaughtExceptions($env = null)
    {
        $env = $env ?: $this->running_env;
        $exceptions = [];
        if (isset($this->exceptions[$env])) {
            $exceptions = $this->exceptions[$env];
        }

        return $exceptions;
    }

    /**
     * Stops handling an uncaught exception
     * 
     * Defaults to the currently running environment.
     *
     * Note: Passing E_ALL removes all error handling for the given environment.
     * Note: The registered error callable is removed once every type has been removed for the given
     * environment.
     *
     * @param  string|int     $env
     * @param  Exception|null $exception
     *
     * @return bool
     */
    public function removeUncaughtException($env, $exception = null)
    {
        if (func_num_args() == 1) {
            $exception = $env;
            $env = $this->running_env;
        }
        
        $is_removed = false;
        if (isset($this->exceptions[$env])) {
            $exception  = Objects::getFullName($exception);
            $is_removed = (bool)Arrays::remove($this->exceptions[$env], $exception);
        }
        
        return $is_removed;
    }
    
    /**
     * Returns whether errors of the given type are being handled in the given environment
     * 
     * Defaults to the currently running environment.
     * 
     * @param  string|int $env   The environment to check
     * @param  string|int $error The core error to check
     *
     * @return bool
     */
    public function isHandlingCoreError($env, $error = 0)
    {
        if (func_num_args() == 1) {
            $error = $env;
            $env   = $this->running_env;
        }
        
        $error = Errors::toInteger($error);
        return isset($this->errors[$env])
            && (
                in_array($error, $this->errors[$env]) ||
                in_array(E_ALL, $this->errors[$env])
            );
    }

    /**
     * Returns whether the given exception is being handled in the given environment
     *
     * Defaults to the currently running environment.
     *
     * @param  string|int             $env       The environment to check
     * @param  string|Exception|null  $exception The exception to check
     *
     * @return bool
     */
    public function isHandlingUncaughtException($env, $exception = null)
    {
        if (func_num_args() == 1) {
            $exception = $env;
            $env = $this->running_env;
        }
        if (is_object($exception)) {
            $exception = Objects::getFullName($exception);
        }
        
        $is_handling = false;
        if (isset($this->exceptions[$env])) {
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
     * @param Exception $exception The exception to handle
     *
     * @throws Exception Rethrows the exception when it's not being handled
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
     * Returns the callback being used to handle core errors.
     *
     * @return callable
     */
    public function getCoreErrorHandler()
    {
        return [$this, self::HANDLER_CORE_ERRORS];
    }

    /**
     * Returns the callback being used to handle uncaught exceptions.
     *
     * @return callable
     */
    public function getUncaughtExceptionHandler()
    {
        return [$this, self::HANDLER_UNCAUGHT_EXCEPTIONS];
    }

    /**
     * Calls the set error callback
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