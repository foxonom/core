<?php
namespace Headzoo\Utilities;
use Headzoo\Utilities\Exceptions\UnexpectedValueException;

/**
 * Thrown when trying to access a class constant which does not exist.
 */
class UndefinedConstantException
    extends UnexpectedValueException {}