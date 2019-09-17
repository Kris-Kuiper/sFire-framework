<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\OTP\Lib;

use sFire\OTP\Resource\Base32;
use sFire\OTP\Lib\AbstractOTP;

class OTP extends AbstractOTP {


    /**
     * @var string $secret 
     */
    protected $secret;


    /**
     * @var string $algorithm
     */
    public $algorithm = 'sha1';


    /**
     * @var integer $digits
     */ 
    public $digits = 6;

    
    /**
     * Constructor
     * @param string $secret
     * @param array $options
     */
    public function __construct($secret, $options = []) {

        $this -> secret     = $secret;
        $this -> digits     = isset($options['digits']) ? $options['digits'] : $this -> digits;
        $this -> algorithm  = isset($options['algorithm']) ? $options['algorithm'] : $this -> algorithm;
    }


    /**
     * Generate a one-time password
     * @param integer $input : number used to seed the hmac hash function.
     * @return integer
     */
    protected function generateOTP($input) {

        $hash = hash_hmac($this -> algorithm, $this -> intToBytestring($input), $this -> byteSecret());
        $hmac = [];

        foreach(str_split($hash, 2) as $hex) {
            $hmac[] = hexdec($hex);
        }

        $offset = $hmac[count($hmac) - 1] & 0xF;
        $code   = ($hmac[$offset + 0] & 0x7F) << 24 | ($hmac[$offset + 1] & 0xFF) << 16 | ($hmac[$offset + 2] & 0xFF) << 8 | ($hmac[$offset + 3] & 0xFF);
        $otp    = $code % pow(10, $this -> digits);

        return str_pad((string) $otp, $this -> digits, '0', STR_PAD_LEFT);
    }


    /**
     * Returns the binary value of the base32 encoded secret
     * @return binary
     */
    protected function byteSecret() {
        return Base32 :: decode($this->secret);
    }


    /**
     * Turns an integer in a OATH bytestring
     * @param integer $int
     * @return string bytestring
     */
    protected function intToBytestring($int) {

        $result = [];

        while($int != 0) {
            
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }
        
        return str_pad(join(array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }
}