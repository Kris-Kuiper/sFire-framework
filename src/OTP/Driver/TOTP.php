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

class TOTP extends OTP {
    

    /**
     * @var integer $interval
     */
    private $interval = 30;


    /**
     * Constructor
     * @param string $secret
     * @param array $options
     */
    public function __construct($secret, $options = []) {

        $this -> interval = isset($options['interval']) ? $options['interval'] : $this -> interval;
        parent :: __construct($secret, $options);
    }


    /**
     * Get the password for a specific timestamp value
     * @param integer $timestamp
     * @return integer
     */
    public function timestamp($timestamp) {
        return $this -> generateOTP($this -> timecode($timestamp));
    }


    /**
     * Get the password for the current timestamp value
     * @return integer
     */
    public function now() {
        return $this -> generateOTP($this -> timecode(time()));
    }


    /**
     * Verify if a password is valid for a specific counter value
     * @param integer $otp
     * @param integer $timestamp 
     * @return boolean
     */
    public function verify($otp, $timestamp = null) {

        if($timestamp === null) {
            $timestamp = time();
        }

        return ($otp == $this -> timestamp($timestamp));
    }


    /**
     * Returns the uri for a specific secret for totp method.
     * @param string $name
     * @return string
     */
    public function getProvisioningUrl($name) {
        return 'otpauth://totp/' . urlencode($name) . '?secret=' . $this -> secret;
    }


    /**
     * Transform a timestamp in a counter based on specified internal
     * @param integer $timestamp
     * @return integer
     */
    protected function timecode($timestamp) {
        return (int) ((((int) $timestamp * 1000) / ($this -> interval * 1000)));
    }
}