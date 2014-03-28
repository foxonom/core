<?php
namespace Headzoo\Core;
use psr\Log\LoggerInterface;
use psr\Log\LogLevel;
use psr\Log\NullLogger;

/**
 * A simple profiling class.
 * 
 * Examples:
 * ```php
 * // Basic usage.
 * $profiler = new Profiler();
 * $profiler->start();
 * 
 * // ... some operation you want to profile ...
 * 
 * $profiler->stop();
 *
 * // Outputs:
 * // "Profile time for 'default': 0.00030207633972168"
 * 
 * // Getting the profile information instead of displaying it.
 * $profiler = new Profiler();
 * $profiler->start();
 * 
 * // ... some operation you want to profile ...
 * 
 * $microtime = $profile->stop(false);
 * var_dump($microtime);
 * 
 * // double(0.00030207633972168)
 * 
 * // Giving an ID to the profile info.
 * $profiler = new Profiler();
 * $profiler->start("profile1");
 * 
 * // ... some operation you want to profile ...
 *
 * $profiler->stop("profile1");
 *
 * // Outputs:
 * // "Profile time for 'profile1': 0.00030207633972168"
 * 
 * // Nested profiling.
 * $profiler = new Profiler();
 * $profiler->start("profile1");
 * 
 * // ... first operation you want to profile ...
 * 
 * $profile->start("profile2");
 * 
 * // ... second operation you want to profile ...
 * 
 * $profiler->stop("profile2");
 * $profiler->stop("profile1");
 *
 * // Outputs:
 * // "Profile time for 'profile2': 0.00030207633972168"
 * // "Profile time for 'profile1': 0.000202397223"
 * 
 * // Using the factory method.
 * $profiler = Profiler::factory();
 * $profiler->start();
 *
 * // ... some operation you want to profile ...
 *
 * $profiler->stop();
 *
 * // Outputs:
 * // "Profile time for 'default': 0.00030207633972168"
 * 
 * // Have the profiler started from the factory method.
 * $profiler = Profiler::factory(true);
 * 
 * // ... some operation you want to profile ...
 * 
 * $profiler->stop();
 * 
 * // Have the profiler started with an ID from the factory method.
 * $profiler = Profiler::factory("profile1");
 * 
 * // .. some operation you want to profile ...
 * 
 * $profiler->stop();
 * 
 * // Profiling can be globally enabled and disabled.
 * Profiler::enabled(false);
 * 
 * // You can also log the profile information with an instance of psr/Log/LoggerInterface.
 * $profiler = new Profiler(new FileLogger());
 * ```
 */
class Profiler
    extends Obj
{
    /**
     * The default enabled value
     */
    const DEFAULT_ENABLED = true;

    /**
     * The default profiling id
     */
    const DEFAULT_ID = "default";
    
    /**
     * Whether profiling is enabled
     * @var bool
     */
    protected static $enabled = self::DEFAULT_ENABLED;
    
    /**
     * Array of microtimes
     * @var array
     */
    protected $start = [];

    /**
     * Logs profile times
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * The logging level
     * @var string
     */
    protected $log_level;

    /**
     * Gets or sets whether profiling is globally enabled
     * 
     * Globally enables or disables profiling when $enabled is set to a boolean value. Returns whether profiling
     * is enabled when called without any argument.
     * 
     * @param  null|bool $enabled Enables or disables profiling
     * @return bool
     */
    public static function enabled($enabled = null)
    {
        if (null === $enabled) {
            return self::$enabled;
        }
        return self::$enabled = (bool)$enabled;
    }

    /**
     * Factory method
     * 
     * Creates an returns a new Profiler instance. The returned profiler will have already started
     * profiling when $id is non-false value. When $id is set to boolean true, the profiling
     * will be started with the default ID. Otherwise the value of $id will be used as the ID.
     * 
     * Examples:
     * ```php
     * $profiler = Profiler::factory();
     * var_dump($profiler->isStarted());
     * // Outputs: bool(false)
     * 
     * $profiler = Profiler::factory(true);
     * var_dump($profiler->isStarted());
     * // Outputs: bool(true)
     * 
     * $profiler = Profiler::factory("profile1");
     * var_dump($profiler->isStarted("profile1"));
     * // Outputs: bool(true)
     * ```
     * 
     * @param  null|string|bool $id Start profiling, or start profiling with this id
     * @return Profiler
     */
    public static function factory($id = null)
    {
        $profiler = new Profiler();
        if (true === $id) {
            $profiler->start();    
        } else if ($id) {
            $profiler->start($id);
        }
        
        return $profiler;
    }
    
    /**
     * Constructor
     * 
     * @param LoggerInterface $logger    Used to log profiling information
     * @param string          $log_level The logging level
     */
    public function __construct(LoggerInterface $logger = null, $log_level = LogLevel::DEBUG)
    {
        if (!$logger) {
            $logger = new NullLogger();
        }
        $this->setLogger($logger, $log_level);
    }

    /**
     * Sets the logger instance
     * 
     * @param  LoggerInterface $logger    Used to log profiling information
     * @param  string          $log_level The logging level
     * @return $this
     */
    public function setLogger(LoggerInterface $logger, $log_level = LogLevel::DEBUG)
    {
        $this->logger    = $logger;
        $this->log_level = $log_level;
        return $this;
    }
    
    /**
     * Resets the profiling state
     * 
     * Stops any profiling which may have been started.
     * 
     * This method always returns true.
     * 
     * Examples:
     * ```php
     * $profiler = new Profiler();
     * $profiler->start();
     * var_dump($profiler->isStarted();
     * $profiler->reset();
     * var_dump($profiler->isStarted());
     * 
     * // Outputs:
     * // bool(true)
     * // bool(false)
     * ```
     * 
     * @return bool
     */
    public function reset()
    {
        foreach($this->start as $id) {
            $this->stop($id, false);
        }
        
        return true;
    }
    
    /**
     * Start profiling
     * 
     * The default profiling ID will be used when none is given.
     * 
     * @param  string $id Identifies this profiling operation
     * @return int
     */
    public function start($id = self::DEFAULT_ID)
    {
        $microtime = false;
        if (self::$enabled) {
            $id = (!$id) ? self::DEFAULT_ID : (string)$id;
            if (isset($this->start[$id])) {
                $this->toss(
                    "Profiling",
                    "Profiling has already started for identifier '{0}'.",
                    $id
                );
            }
            
            $this->start[$id] = $microtime = microtime(true);
        }
        
        return $microtime;
    }

    /**
     * Stop profiling
     *
     * Stops profiling for the given $id, and returns and/or displays the number of microseconds which
     * have elapsed since the call to ::start() with the same $id. The default profiling ID will be used
     * when none is given.
     *
     * Displays the profiling time when $display is set to true. A boolean value may be passed as the first
     * argument to this method to control displaying output, and the default $id value will be used.
     * The time elapsed is still returned.
     * 
     * Examples:
     * ```php
     * $profiler = new Profiler();
     * $profiler->start();
     * usleep(100);
     * $micro = $profiler->stop();
     * var_dump($micro);
     * 
     * // Outputs:
     * // "Profile time for 'default': 0.00030207633972168"
     * // double(0.00030207633972168)
     *
     * $profiler->start();
     * usleep(100);
     * $micro = $profiler->stop(false);
     * var_dump($micro);
     *
     * // Outputs:
     * // double(0.00030207633972168)
     *
     * $profiler->start("profile1");
     * usleep(100);
     * $micro = $profiler->stop("profile1");
     * var_dump($micro);
     *
     * // Outputs:
     * // "Profile time for 'profile1': 0.00030207633972168"
     * // double(0.00030207633972168)
     * ```
     * 
     * @param  string $id      Identifies this profiling operation
     * @param  bool   $display Whether or not to display the profiling data
     * @return bool
     * @throws Exceptions\ProfilingException When called without first calling ::start() with the same id
     */
    public function stop($id = self::DEFAULT_ID, $display = true)
    {
        $microtime = false;
        if (self::$enabled) {
            if (is_bool($id)) {
                $display = $id;
                $id      = self::DEFAULT_ID;
            }
            $id = (!$id) ? self::DEFAULT_ID : (string)$id;
            if (!isset($this->start[$id])) {
                $this->toss(
                    "Profiling",
                    "{me}::stop({0}) called without calling {me}::start({0}) first.",
                    $id
                );
            }
            
            $microtime = microtime(true) - $this->start[$id];
            unset($this->start[$id]);
            $message = printf(
                "Time for profile '%s': %F",
                $id,
                $microtime
            );
            $this->logger->log($this->log_level, $message);
            if ($display) {
                echo $message, PHP_EOL;
            }
        }
        
        return $microtime;
    }

    /**
     * Returns whether profiling has been started for the given id
     * 
     * @param  string $id Identifies this profiling operation
     * @return bool
     */
    public function isStarted($id = self::DEFAULT_ID)
    {
        $id = (!$id) ? self::DEFAULT_ID : (string)$id;
        return isset($this->start[$id]);
    }
} 