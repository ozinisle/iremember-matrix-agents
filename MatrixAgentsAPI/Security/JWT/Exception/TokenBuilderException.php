<?php namespace MatrixAgentsAPI\Security\JWT\Exception;

use Exception;

/**
 * Simple exception interface class for the Token Builder class to make
 * exceptions more specific and obvious. Extends the PHP exception class
 *
 * @author Rob Waller <rdwaller1984@gmail.com>
 */
class TokenBuilderException extends Exception
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
