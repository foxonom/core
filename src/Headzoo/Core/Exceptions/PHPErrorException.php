<?php
namespace Headzoo\Core\Exceptions;

/**
 * Thrown when PHP triggers an error.
 */
class PHPErrorException
    extends Exception
{
    /**
     * Constructor
     * 
     * @param string     $message   The error message
     * @param int        $code      The error code
     * @param string     $file      The file where the error occurred
     * @param int        $line      The line where the error occurred
     * @param \Exception $prev      Previous exception
     */
    public function __construct($message, $code, $file, $line, \Exception $prev = null)
    {
        parent::__construct($message, $code, $prev);
        $this->file = $file;
        $this->line = $line;
    }
}