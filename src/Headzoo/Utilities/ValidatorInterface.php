<?php
namespace Headzoo\Utilities;
use InvalidArgumentException;
use Exception;

/**
 * Interface for classes which perform data validation.
 */
interface ValidatorInterface
{
    /**
     * The default type of exception thrown when a validation fails
     */
    const DEFAULT_THROWN_EXCEPTION = Exceptions\ValidationFailedException::class;

    /**
     * Sets the default thrown exception class name
     *
     * @param  string $thrownException Name of an Exception class to throw
     * @return $this
     * @throws InvalidArgumentException When $thrownException does not name a sub-class of Exception
     */
    public function setThrownException($thrownException);

    /**
     * Throws an exception when required values are missing from an array of key/value pairs
     *
     * The $values argument is an array of key/value pairs, and the $required argument is an array
     * of keys which must exist in $values to validate. When $allowEmpty is false, the required values
     * must also evaluate to a non-empty value to validate.
     *
     * Use the Validator::setThrownException() method to set which type of exception is thrown.
     *
     * This method always returns true, but throws an exception when the value is invalid.
     *
     * @param  array $values       The values to validate
     * @param  array $required     List of keys
     * @param  bool  $allowEmpty   Are empty values acceptable?
     * @return bool
     * @throws Exception When a required value is missing
     */
    public function validateRequired(array $values, array $required, $allowEmpty = false);
} 