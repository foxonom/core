<?php
namespace Headzoo\Core;
use Psr\Log;

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
 * // You can also log the profile information with an instance of Psr/Log/LoggerInterface.
 * $profiler = new Profiler(new FileLogger());
 * ```
 */
class Profiler
    extends Obj
    implements Log\LoggerAwareInterface
{
    use Log\LoggerAwareTrait;
    use FunctionsTrait;
    
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
     * Profiles one or more calls to a function
     * 
     * Calls a function the number of times specified by $num_runs, and displays the total run time,
     * the average run time, and the time for each run. Also returns an array with the same
     * information.
     * 
     * Regardless of the number of runs, the output only shows 40 run times: the first 20 and the
     * last 20. The returned array always contains the times for each run.
     * 
     * Note: Profiling must be enabled to use this method.
     * 
     * Examples:
     * ```php
     * // This example runs the closure 100 times, and displays the profile results.
     * $runs = Profiler::run(100, true, function() {
     *      ... do something here ...
     * });
     * 
     * // Output:
     * //
     * // Total Runs:                 100
     * // Total Time:      0.099596977234
     * // Average Time:    0.000981624126
     * // -------------------------------
     * // Run #1           0.000479936599
     * // Run #2           0.000968933105
     * // Run #3           0.000982999801
     * // Run #4           0.000988006591
     * // ......
     * // Run #97          0.000985145568
     * // Run #98          0.000983953476
     * // Run #99          0.000997066497
     * // Run #100         0.000993013382
     * 
     * print_r($runs);
     * // Output:
     * // [
     * //       "total"   => 0.099596977234,
     * //       "average" => 0.000981624126,
     * //       "runs"    => [
     * //           0.0004799365997,
     * //           0.0009689331055,
     * //           ....
     * //           0.0009930133820
     * //       ]
     * // ]
     * 
     * // Use false for the second argument to turn off displaying the results.
     * $runs = Profiler::run(100, false, function() {
     *      ... do something here ...
     * });
     * 
     * // The middle argument may be omitted, and defaults to true.
     * $runs = Profiler::run(100, function() {
     *      ... do something here ...
     * });
     * ```
     * 
     * @param  int      $num_runs  The number of times to run the function
     * @param  bool     $display    Whether or not to display the results
     * @param  callable $callable   The function to call
     *
     * @return array
     */
    public static function run($num_runs, $display = true, callable $callable = null)
    {
        self::swapCallable($display, $callable, true);
        
        $results = [];
        if (self::$enabled) {
            $profiler  = new Profiler();
            $run_times = [];
            $memory    = memory_get_usage();
            for($i = 0; $i < $num_runs; $i++) {
                $profiler->start("loop");
                $callable();
                $run_times[] = $profiler->stop("loop", false);
            }
            $results["memory"]  = memory_get_usage() - $memory;
            $results["total"]   = array_sum($run_times);
            $results["average"] = $results["total"] / $num_runs;
            $results["runs"]    = $run_times;
            
            if ($display) {
                $output  = sprintf("Total Runs:   %17s%s",    number_format($num_runs),PHP_EOL);
                $output .= sprintf("Total Time:   %17.12f%s", $results["total"], PHP_EOL);
                $output .= sprintf("Average Time: %17.12f%s", $results["average"], PHP_EOL);
                $output .= sprintf("Total Memory: %17s%s",    Conversions::bytesToHuman($results["memory"]), PHP_EOL);
                $output .= sprintf('-------------------------------%s', PHP_EOL);
                
                if (count($run_times) > 50) {
                    $runs   = [];
                    $runs[] = array_slice($run_times, 0, 20);
                    $runs[] = array_slice($run_times, -20, null, true);
                } else {
                    $runs[] = $run_times;
                }
                foreach($runs as $run) {
                    foreach($run as $index => $microtime) {
                        $output .= sprintf("Run #%-7d %18.12f%s", ++$index, $microtime, PHP_EOL);
                        if (20 == $index) {
                            $output .= "......." . PHP_EOL;
                        }
                    }
                }
                
                echo $output;
            }
        }
        
        return $results;
    }
    
    /**
     * Constructor
     * 
     * @param Log\LoggerInterface $logger    Used to log profiling information
     */
    public function __construct(Log\LoggerInterface $logger = null)
    {
        if (!$logger) {
            $logger = new Log\NullLogger();
        }
        $this->setLogger($logger);
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
     * @return float|bool
     * @throws Exceptions\ProfilingException When called without first calling ::start() with the same id
     */
    public function stop($id = self::DEFAULT_ID, $display = true)
    {
        $stopped   = microtime(true);
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
            
            $microtime = $stopped - $this->start[$id];
            unset($this->start[$id]);
            $message = sprintf(
                "Time for profile '%s': %F",
                $id,
                $microtime
            );
            $this->logger->info($message);
            if ($display) {
                echo $message, PHP_EOL;
            }
        }
        
        return $microtime;
    }

    /**
     * Returns whether profiling has been started for the given id
     * 
     * Examples:
     * ```php
     * $profiler = new Profiler();
     * $is_started = $profiler->isStarted();
     * var_dump($is_started);
     * // Outputs: bool(false);
     * 
     * $profiler->start();
     * $is_started = $profiler->isStarted();
     * var_dump($is_started);
     * // Outputs: bool(true)
     * ```
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