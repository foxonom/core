<?php
namespace Headzoo\Core\Exceptions;

/**
 * Thrown when trying to access a class constant which does not exist.
 */
class UndefinedConstantException
    extends LogicException {}