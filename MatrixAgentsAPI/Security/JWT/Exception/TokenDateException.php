<?php namespace MatrixAgentsAPI\Security\JWT\Exception;
ini_set('display_errors', 1);
error_reporting(E_ALL);
use Exception;

/**
 * Simple exception interface class for the DateTime Helper class to make
 * exceptions more specific and obvious. Extends the PHP exception class
 *
 * @author Rob Waller <rdwaller1984@gmail.com>
 */
class TokenDateException extends Exception
{
    /**
     * Constructor for the Token Builder Exception class
     *
     * @param string $message
     * @param int $code
     * @param string $previous
     */
    public function __construct(string $message, int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
