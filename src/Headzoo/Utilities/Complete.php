<?php
namespace Headzoo\Utilities;

/**
 * Used to call a function when the object destructs.
 *
 * The class wraps a callable function, which is called in the class destructor. The utility
 * of this scheme is the ability to ensure the function is called eventually. Usually when
 * the Complete object goes out of scope, which is when it's destructor is called.
 * 
 * This class can be used to simulate a try...catch...finally in versions of PHP which do not
 * support the finally clause.
 * 
 * Example:
 * ```php
 * // In this example the database connection will always be closed, even if the $database->fetch()
 * // method throws an exception, because the anonymous function passed to Complete::factory()
 * // is called when the $complete object goes out of scope.
 * 
 * $database = new FakeDatabase();
 * $complete = Complete::factory(function() use($database) {
 *      $database->close();
 * });
 * try {
 *      $rows = $database->fetch();
 * } catch (Exception $e) {
 *      echo $e->getTraceAsString();
 *      throw $e;
 * }
 * ```
 * 
 * @see http://blogs.balabit.com/2011/02/20/try-catch-finally-in-php/
 */
class Complete
{
    /**
     * Function to be called in the destructor
     * @var callable
     */
    private $callable;

    /**
     * Static factory class to create a new instance of this class
     *
     * Example:
     * ```php
     * $complete = Complete::factory(function() {
     *      echo "I'm complete!";
     * });
     * ```
     * 
     * @param  callable $callable The function to call in the Complete destructor
     * @return Complete
     */
    public static function factory(callable $callable)
    {
        return new Complete($callable);
	}

    /**
     * Constructor
     *
     * @param callable $callable The function to call in the destructor
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
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
     * $complete = Complete::factory(function() {
     *      echo "I'm complete!";
     * });
     * $complete();
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
     * $complete = Complete::factory(function() {
     *      echo "I'm complete!";
     * });
     * $complete->invoke();
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
            call_user_func($callable);
            $is_called = true;
        }
        
        return $is_called;
    }
} 