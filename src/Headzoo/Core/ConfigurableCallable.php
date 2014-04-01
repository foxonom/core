<?php
namespace Headzoo\Core;
use Headzoo\Core\Exceptions\PHPException;
use Exception;
use psr\Log;

/**
 * Creates a callable instance with configurable behavior.
 * 
 * Primarily used when you need to continuously call a method or function until certain
 * conditions are met. For example until an exception isn't being thrown, or a specific
 * value is returned.
 * 
 * #### Examples
 * In this example you want to insert a row into the database, which may lead to
 * a DeadLockException being thrown. The recommended action for dead locks is retrying
 * the query. We use a ConfigurableCallable instance to keep trying the query until
 * it succeeds.
 * 
 * ```php
 * // Establish a link to the database, and create a callable wrapping the
 * // mysqli_query() function.
 * $link  = mysqli_connect("localhost", "my_user", "my_password", "my_db");
 * $query = new ConfigurableCallable("mysqli_query");
 *
 * // The retryOnException(DeadLockException:class) call tells $query to keep calling
 * // mysqli_query($link, "INSERT INTO `members` ('headzoo')") until DeadLockException
 * // is no longer being thrown. As many times as needed, or until the $query->max_retries
 * // (defaults to 10) value is reached.
 * $query->retryOnException(DeadLockException::class);
 * $result = $query($link, "INSERT INTO `members` ('headzoo')");
 * ```
 * 
 * In this example we will call a remote web API, which sometimes takes a few tries
 * depending on how busy the remote server is at the any given moment. The remote
 * server may return an empty value (null), the API library may thrown an exception,
 * or PHP may trigger an error.
 * 
 * ```php
 * $api     = new RemoteApi();
 * $members = new ConfigurableCallable([$api, "getMembers"]);
 * 
 * // When calling retryOnException() without the name of a specified exception,
 * // the callable will keep retrying when any kind of exception is thrown.
 * $members->retryOnException()
 *         ->retryOnError()
 *         ->retryOnNull();
 * 
 * // The $members instance will keep trying to call $api->getMembers(0, 10) until
 * // an exception is no longer being thrown, PHP is not triggering any errors,
 * // and the remote server is not returning null.
 * $rows = $members(0, 10);
 * ```
 * 
 * The ConfigurableCallable::setMaxRetries() method is used to limit the number of
 * times before the callable gives up. If the callable does give up, it will return
 * the last value returned by the wrapped function, or throw the last exception
 * thrown by the function. PHP errors are converted to exceptions and thrown.
 * Errors can be logged by passing a psr\Log\LoggerInterface instance to the
 * ConfigurableCallable constructor.
 *
 * ```php
 * $link  = mysqli_connect("localhost", "my_user", "my_password", "my_db");
 * $query = new ConfigurableCallable("mysqli_query", new FileLogger());
 * $query->retryOnException(DeadLockException::class);
 * $query->setMaxRetries(5);
 * 
 * // The $query instance will throw the last caught DeadLockException if the
 * // max retries value is reached without successfully inserting the row. 
 * try {
 *      $result = $query($link, "INSERT INTO `members` ('headzoo')");
 * } catch (DeadLockException $e) {
 *      die("Could not insert row.");
 * }
 * ```
 * 
 * There are dozens of different configuration options, which are listed below.
 * In this example we keep calling a function until it returns a value greater
 * than 42. We'll pass -1 to the setMaxRetries() method, which means retry an
 * unlimited number of times.
 * 
 * ```php
 * $counter = ConfigurableCallable::factory(function() {
 *      static $count = 0;
 *      return ++$count;
 * });
 * $counter->setMaxRetries(-1)
 *         ->retryOnLessThan(42);
 * echo $counter();
 * // Outputs: 42
 * ```
 * 
 * In addition to the retry conditions, there are also return conditions, and throw
 * conditions. In this example we want to call a remote API until it returns true or
 * false.
 * 
 * ```php
 * $api     = new RemoteApi();
 * $members = new ConfigurableCallable([$api, "doesMemberExist"]);
 * $members->returnOnTrue()
 *         ->returnOnFalse();
 * $does_exist = $members("headzoo");
 * ```
 * 
 * These are the methods which are available.
 * 
 * @method ConfigurableCallable retryOnException
 * @method ConfigurableCallable returnOnException
 * @method ConfigurableCallable throwOnException
 * @method ConfigurableCallable retryOnError
 * @method ConfigurableCallable returnOnError
 * @method ConfigurableCallable throwOnError
 * @method ConfigurableCallable retryOnTrue
 * @method ConfigurableCallable returnOnTrue
 * @method ConfigurableCallable retryOnFalse
 * @method ConfigurableCallable returnOnFalse
 * @method ConfigurableCallable retryOnNull
 * @method ConfigurableCallable returnOnNull 
 * @method ConfigurableCallable retryOnInstanceOf
 * @method ConfigurableCallable returnOnInstanceOf 
 * @method ConfigurableCallable retryOnEquals
 * @method ConfigurableCallable returnOnEquals
 * @method ConfigurableCallable retryOnNotEquals
 * @method ConfigurableCallable returnOnNotEquals
 * @method ConfigurableCallable retryOnGreaterThan
 * @method ConfigurableCallable returnOnGreaterThan
 * @method ConfigurableCallable retryOnGreaterThanOrEquals
 * @method ConfigurableCallable returnOnGreaterThanOrEquals
 * @method ConfigurableCallable retryOnLessThan
 * @method ConfigurableCallable returnOnLessThan
 * @method ConfigurableCallable retryOnLessThanOrEquals
 * @method ConfigurableCallable returnOnLessThanOrEquals
 */
class ConfigurableCallable
    extends Obj
    implements Log\LoggerAwareInterface
{
    use FunctionsTrait;
    use Log\LoggerAwareTrait;

    /**
     * Default maximum number of retries
     */
    const DEFAULT_MAX_RETRIES = 10;
    
    /**
     * The possible actions
     * @var array
     */
    private static $actions = [
        "retry",
        "return",
        "throw"
    ];

    /**
     * The possible expressions
     * @var array
     */
    private static $expressions = [
        "Exception" => [
            "req_arg" => true,
            "default" => Exception::class,
            "actions" => ["retry", "return", "throw"],
            "compare" => "isExceptionOf"
        ],
        "Error" => [
            "req_arg" => false,
            "default" => PHPException::class,
            "actions" => ["retry", "return", "throw"],
            "compare" => "isExceptionOf"
        ],
        "Null" => [
            "req_arg" => false,
            "default" => null,
            "actions" => ["retry", "return"],
            "compare" => "isStrictlyEquals"
        ],
        "True" => [
            "req_arg" => false,
            "default" => true,
            "actions" => ["retry", "return"],
            "compare" => "isStrictlyEquals"
        ],
        "False" => [
            "req_arg" => false,
            "default" => false,
            "actions" => ["retry", "return"],
            "compare" => "isStrictlyEquals"
        ],
        "InstanceOf" => [
            "req_arg" => true,
            "actions" => ["retry", "return"],
            "compare" => "isInstanceOf"
        ],
        "Equals" => [
            "req_arg" => true,
            "actions" => ["retry", "return"],
            "compare" => "isEquals"
        ],
        "NotEquals" => [
            "req_arg" => true,
            "actions" => ["retry", "return"],
            "compare" => "isNotEquals"
        ],
        "GreaterThan" => [
            "req_arg" => true,
            "actions" => ["retry", "return"],
            "compare" => "isGreaterThan"
        ],
        "GreaterThanOrEquals" => [
            "req_arg" => true,
            "actions" => ["retry", "return"],
            "compare" => "isGreaterThanOrEquals"
        ],
        "LessThan" => [
            "req_arg" => true,
            "actions" => ["retry", "return"],
            "compare" => "isLessThan"
        ],
        "LessThanOrEquals" => [
            "req_arg" => true,
            "actions" => ["retry", "return"],
            "compare" => "isLessThanOrEquals"
        ]
    ];

    /**
     * The dynamic method regex
     * @var string
     */
    private static $regex;

    /**
     * Max number of retries
     * @var int
     */
    protected $max_retries = self::DEFAULT_MAX_RETRIES;
    
    /**
     * The wrapped callable
     * @var callable
     */
    protected $callback;
    
    /**
     * The configured conditions
     * @var array
     */
    protected $conditions = [];

    /**
     * The filter callback
     * @var callable
     */
    protected $filter;

    /**
     * Factory method
     * 
     * @param callable $callback The callback to invoke
     * 
     * @return ConfigurableCallable
     */
    public static function factory(callable $callback)
    {
        return new ConfigurableCallable($callback);
    }
    
    /**
     * Constructor
     * 
     * @param callable            $callback     The callback to invoke
     * @param Log\LoggerInterface $logger       Used to log errors
     */
    public function __construct(callable $callback, Log\LoggerInterface $logger = null)
    {
        $this->callback = $callback;
        if (!$logger) {
            $logger = new Log\NullLogger();
        }
        $this->setLogger($logger);
    }

    /**
     * Sets the max number of retries
     * 
     * @param $max_retries
     *
     * @return $this
     */
    public function setMaxRetries($max_retries)
    {
        $this->max_retries = $max_retries;
        return $this;
    }

    /**
     * Sets a return value filter
     * 
     * @param callable $filter
     *
     * @return $this
     */
    public function setFilter(callable $filter)
    {
        $this->filter = $filter;
        return $this;
    }
    
    /**
     * @param  string $method The method being called
     * @param  array  $args   The method arguments
     *
     * @return $this
     */
    public function __call($method, $args)
    {
        if (!self::$regex) {
            $actions     = join("|", self::$actions);
            $expressions = join("|", array_keys(self::$expressions));
            self::$regex = "/^({$actions})On({$expressions})$/";
        }
        
        $matched = false;
        if (preg_match(self::$regex, $method, $matches)) {
            $action  = $matches[1];
            $expr    = $matches[2];
            
            if (in_array($action, self::$expressions[$expr]["actions"])) {
                $arg = array_shift($args);
                if (!$arg && self::$expressions[$expr]["req_arg"] && !isset(self::$expressions[$expr]["default"])) {
                    $this->toss(
                        "BadMethodCall",
                        "The method {me}::{0}() expects exactly 1 argument.",
                        $method
                    );
                }

                $matched = true;
                if (!$arg) {
                    $arg = self::$expressions[$expr]["default"];
                }
                $this->conditions[] = [
                    "compare"  => self::$expressions[$expr]["compare"],
                    "value"    => $arg,
                    "action"   => $action
                ];
            }
        }
        
        if (!$matched) {
            $this->toss(
                "BadMethodCall",
                "The method {me}::{0}() does not exist.",
                $method
            );
        }
        
        return $this;
    }

    /**
     * Calls the function
     */
    public function __invoke()
    {
        return call_user_func_array([$this, "invoke"], func_get_args());
    }

    /**
     * Calls the function
     */
    public function invoke()
    {
        $args      = func_get_args();
        $retries   = 0;
        $exception = null;
        $return    = null;

        /** @noinspection PhpUnusedLocalVariableInspection */
        $sc = SmartCallable::factory("restore_error_handler");
        set_error_handler(function($type, $message, $file, $line) {
                throw PHPException::factory(
                    $message,
                    $type,
                    $file,
                    $line
                );
            });

        do {
            try {
                $exception = null;
                $return    = call_user_func_array($this->callback, $args);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
            $action = $this->matchesCondition($return, $exception);
            if ("retry" == $action) {
                $retries++;
            }
        } while("retry" == $action && ($retries < $this->max_retries || -1 === $this->max_retries));
        
        if ($exception && "return" !== $action) {
            throw $exception;
        }
        if ($this->filter) {
            $return = call_user_func($this->filter, $return);
        }
        
        return $return;
    }

    /**
     * Returns which condition the value matches
     * 
     * @param  mixed     $return    The callable return value or thrown exception
     * @param  Exception $exception Any exception that was thrown
     *
     * @return null|string
     */
    protected function matchesCondition($return, $exception = null)
    {
        $matched  = false;
        foreach($this->conditions as $expression) {
            if ("isExceptionOf" === $expression["compare"]) {
                $matched = Comparator::isInstanceOf($exception, $expression["value"]);
            } else {
                $matched = Comparator::$expression["compare"]($return, $expression["value"]);
            }
            if ($matched) {
                $matched = $expression["action"];
                break;
            }
        }
        
        return $matched;
    }
} 