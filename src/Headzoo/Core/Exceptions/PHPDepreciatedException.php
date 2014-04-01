<?php
namespace Headzoo\Core\Exceptions;

/**
 * Thrown when PHP triggers an E_DEPRECIATED or E_USER_DEPRECIATED.
 */
class PHPDepreciatedException
    extends PHPException {}