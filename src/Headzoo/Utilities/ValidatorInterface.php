<?php
namespace Headzoo\Utilities;
use InvalidArgumentException;

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
} 