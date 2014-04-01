<?php
namespace Headzoo\Core\Exceptions;

/**
 * Thrown when PHP triggers an error.
 */
class PHPException
    extends Exception
{
    /**
     * Returns an exception for the given code
     * 
     * The value of $code should be one of the E_ERROR constants. This method will return the
     * correct PHPException instance for that code. For example, if $code == E_WARNING, an
     * instance of PHPWarningException is returned.
     * 
     * @param  string    $message   The error message
     * @param  int       $code      The error code
     * @param  string    $file      The file where the error occurred
     * @param  int       $line      The line in the file where the error occurred
     * @param \Exception $prev      The previous exception
     * 
     * @return PHPException
     */
    public static function factory($message, $code, $file, $line, \Exception $prev = null)
    {
        $exception = null;
        switch($code) {
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_CORE_WARNING:
                $exception = PHPWarningException::class;
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $exception = PHPNoticeException::class;
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $exception = PHPDepreciatedException::class;
                break;
            case E_STRICT:
                $exception = PHPStrictException::class;
                break;
            case E_PARSE:
                $exception = PHPParseException::class;
                break;
            default:
                $exception = PHPErrorException::class;
                break;
        }
        
        return new $exception(
            $message,
            $code,
            $file,
            $line,
            $prev
        );
    }
    
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