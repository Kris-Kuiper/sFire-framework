<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\OTP\Driver;

use sFire\OTP\Lib\OTP;

class HOTP extends OTP {
   

    /**
     * Get the password for a specific counter value
     * @param integer $count 
     * @return integer
     */
    public function counter($count) {
        return $this -> generateOTP($count);
    }


    /**
     * Verify if a password is valid for a specific counter value
     * @param integer $otp
     * @param integer $counter
     * @return boolean
     */
    public function verify($otp, $counter) {
        return $otp == $this -> counter($counter);
    }


    /**
     * Returns the uri for a specific secret for hotp method.
     * @param string $name
     * @param integer $initial_count
     * @return string
     */
    public function getProvisioningUrl($name, $initial_count) {
        return 'otpauth://hotp/' . urlencode($name) . '?secret=' . $this -> secret . '&counter=' . $initial_count;
    }
}