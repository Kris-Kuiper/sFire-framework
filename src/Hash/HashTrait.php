<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Hash;

trait HashTrait {


    /**
     * Encrypt and authenticate
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function encrypt($data, $key) {

        static :: check();
        
        $iv = static :: random_bytes(16);
        
        //Encryption
        $ciphertext = openssl_encrypt($data, self :: METHOD, mb_substr($key, 0, 32, self :: ENCODING), OPENSSL_RAW_DATA, $iv);

        //Authentication
        $hmac = hash_hmac(self :: ALGORITHM, $iv . $ciphertext, mb_substr($key, 32, null, self :: ENCODING), true);

        return $hmac . $iv . $ciphertext;
    }


    /**
     * Decrypt a string
     * @param string $data
     * @param mixed $salt
     * @return mixed
     */
    public static function decrypt($data, $key) {

        static :: check();

        $hmac       = mb_substr($data, 0, 32, self :: ENCODING);
        $iv         = mb_substr($data, 32, 16, self :: ENCODING);
        $ciphertext = mb_substr($data, 48, null, self :: ENCODING);

        //Authentication
        $hmacNew = hash_hmac(self :: ALGORITHM, $iv . $ciphertext, mb_substr($key, 32, null, self :: ENCODING), true);

        if(true === static :: hash_equals($hmac, $hmacNew)) {
            return openssl_decrypt($ciphertext, self :: METHOD, mb_substr($key, 0, 32, self :: ENCODING), OPENSSL_RAW_DATA, $iv);
        }

        return null;
    }


    /**
     * Hashes text and returns it
     * @param string $data
     * @return string
     */
    public static function hash($data) {
        
        if(false === is_string($data) && false === is_numeric($data)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or number, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
        }

        return hash(self :: HASH_ALGORITHM, $data);
    }


    /**
     * Verifies that data matches a hash
     * @param string $data
     * @param string $hash
     * @return boolean
     */
    public static function validateHash($data, $hash) {

        if(false === is_string($data) && false === is_numeric($data)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or number, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
        }

        if(false === is_string($hash)) {
            return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($hash)), E_USER_ERROR);
        }

        return hash(self :: HASH_ALGORITHM, $data) === $hash;
    }


    /**
     * Hash equals function for PHP 5.5+
     * @param string $expected
     * @param string $actual
     * @return bool
     */
    private static function hash_equals($expected, $actual) {

        $expected = (string) $expected;
        $actual   = (string) $actual;
        
        if(true === function_exists('hash_equals')) {
            return hash_equals($expected, $actual);
        }

        $lenExpected  = mb_strlen($expected, self :: ENCODING);
        $lenActual    = mb_strlen($actual, self :: ENCODING);
        $len          = min($lenExpected, $lenActual);
        $result       = 0;
        
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }

        $result |= $lenExpected ^ $lenActual;

        return ($result === 0);
    }


    /**
     * Random_bytes equals function for PHP 5.5+
     * @param int $length
     * @return string
     */
    private static function random_bytes($length) {

        if(true == function_exists('random_bytes')) {
            return random_bytes($length);
        }

        return openssl_random_pseudo_bytes($length);
    }


    /**
     * Check if needed functions/extensions are loaded
     */
    private static function check() {

        foreach(['openssl'] as $extension) {

            if(false === extension_loaded($extension)) {
                return trigger_error(sprintf('Extension "%s" should be loaded to use %s', $extension, __CLASS__), E_USER_ERROR);
            }
        }
    }
}
?>