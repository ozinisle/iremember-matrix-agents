<?php namespace MatrixAgentsAPI\Security\JWT\Helper;
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * A very simple class that provides an interface to the php hash_hmac method to
 * hash strings
 */
class Hmac
{
    /**
     * Hash a string and always return the output as raw binary.
     *
     * @param string $hash
     * @param string $string
     * @param string $secret
     *
     * @return string
     */
    public static function hash(string $hash, string $string, string $secret) : string
    {
        return hash_hmac($hash, $string, $secret, true);
    }
}
