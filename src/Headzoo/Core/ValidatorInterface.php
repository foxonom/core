<?php
namespace Headzoo\Core;
use Exception;

/**
 * Interface for classes which perform data validation.
 */
interface ValidatorInterface
{
    /**
     * Throws an exception when required values are missing from an array of key/value pairs
     *
     * The $values argument is an array of key/value pairs, and the $required argument is an array
     * of keys which must exist in $values to validate. When $allowEmpty is false, the required values
     * must also evaluate to a non-empty value to validate.
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