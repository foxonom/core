<?php
namespace Headzoo\Core;

/**
 * Used to call a function when the object destructs.
 *
 * The class wraps a callable function, which is called in the class destructor. The utility
 * of this scheme is the ability to ensure the function is called eventually. Usually when
 * the SmartCallable object goes out of scope, which is when it's destructor is called.
 * 
 * This class can be used to simulate a try...catch...finally in versions of PHP which do not
 * support the finally clause.
 * 
 * Example:
 * ```php
 * // In this example we create a method which requests a web resource using curl.
 * // We use a SmartCallable instance to ensure the curl resource is closed when
 * // the method returns, or an exception is thrown.
 * public function fetch()
 * {
 *      $curl = curl_init("http://some-site.com");
 *      $sc = SmartCallable::factory(function() use($curl) {
 *              curl_close($curl);
 *      });
 * 
 *      return curl_exec($curl);
 * }
 * ```
 * 
 * @see http://blogs.balabit.com/2011/02/20/try-catch-finally-in-php/
 */
class SmartCallable
    extends Obj
{
    /**
     * Function to be called in the destructor
     * @var callable
     */
    private $callable;

    /**
     * Arguments passed to the callable
     * @var array
     */
    private $args = [];

    /**
     * Static factory class to create a new instance of this class
     *
     * Example:
     * ```php
     * $sc = SmartCallable::factory(function() {
     *      echo "I'm complete!";
     * });
     * 
     * // Passing arguments to the callable.
     * $sc = SmartCallable::factory(function($arg1, $arg1) {
     *      echo "{$arg1}, {$arg2}";
     * }, "Hello", "World");
     * 
     * // Using a string as the callable, and passing arguments.
     * $curl = curl_init("http://some-site.com");
     * $sc = SmartCallable::factory("curl_close", $curl);
     * ```
     * 
     * @param  callable $callable The function to call in the Complete destructor
     * @param  mixed    $args     Arguments passed to the callable
     *                           
     * @return SmartCallable
     */
    public static function factory(callable $callable, $args = null)
    {
        $args     = func_get_args();
        $callable = array_shift($args);
        
        return new SmartCallable($callable, $args);
	}

    /**
     * Constructor
     *
     * @param callable $callable The function to call in the destructor
     * @param array    $args     Arguments passed to the callable
     */
    public function __construct(callable $callable, array $args = [])
    {
        $this->callable = $callable;
        $this->args     = $args;
    }

    /**
     * Destructor
     *
     * Calls the set $callable function
     */
    public function __destruct()
    {
        $this->invoke();
    }

    /**
     * When calling an instance of this class as a function
     *
     * Example:
     * ```php
     * $sc = SmartCallable::factory(function() {
     *      echo "I'm complete!";
     * });
     * $sc();
     * ```
     * @see http://www.php.net/manual/en/language.oop5.magic.php#object.invoke
     * @return bool
     */
    public function __invoke()
    {
        return $this->invoke();
    }

    /**
     * Calls the set $callable function
     * 
     * Ensures the callable cannot be called twice, even if the callback has an error. Subsequent calls to this method
     * do nothing. Returns true when the callable was called, and false if not.
     *
     * Example:
     * ```php
     * $sc = SmartCallable::factory(function() {
     *      echo "I'm complete!";
     * });
     * $sc->invoke();
     * ```
     * 
     * @return bool
     */
    public function invoke()
    {
        $is_called = false;
        if ($this->callable) {
            $callable       = $this->callable;
            $this->callable = null;
            call_user_func_array($callable, $this->args);
            $is_called = true;
        }
        
        return $is_called;
    }
} 