<?php
namespace Headzoo\Core\Exceptions;

/**
 * Thrown when PHP triggers an E_WARNING, E_USER_WARNING, E_CORE_WARNING, or E_COMPILE_WARNING.
 */
class PHPWarningException
    extends PHPException {}