<?php
namespace Headzoo\Core\Exceptions;

/**
 * Thrown when PHP triggers an E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR, E_CORE_ERROR or E_COMPILE_ERROR.
 */
class PHPErrorException
    extends PHPException {}