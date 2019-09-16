<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\OTP;

use sFire\OTP\Resource\Base32;

class OTP {


	/**
     * @var string $driver
     */
    private $driver;


    /**
     * @var mixed $driverInstance
     */
    private $driverInstance;


    /**
     * @var integer $digits
     */
    private $digits = 6;


    /**
     * @var integer $interval
     */
    private $interval = 30;


    /**
     * @var string $secret
     */
    public $secret;


    /**
     * @var string $algorithm
     */
    public $algorithm = 'sha1';


	/**
	 * Sets the driver
	 * @param string $driver
	 * @return $this
	 */
    public function setDriver($driver) {

    	if(false === is_string($driver)) {
    		return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($driver)), E_USER_ERROR);
    	}

    	$driver = __NAMESPACE__ . '\\Driver\\' . strtoupper($driver);

    	if(false === class_exists($driver)) {
    		return trigger_error(sprintf('Driver "%s" does not exists', $driver), E_USER_ERROR);
    	}

        $this -> driver = $driver;

        return $this;
    }


    /**
     * Set a secret key
     * @param string $secret
     * @return $this
     */
    public function setSecret($secret) {
    	
        if(false === is_string($secret)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($secret)), E_USER_ERROR);
        }

        $this -> driverInstance = null;
        $this -> secret = $secret;

        return $this;
    }


    /**
     * Set the type of a algorithm to use
     * @param string $algorithm
     * @return $this
     */
    public function setAlgorithm($algorithm) {
    	
        if(false === is_string($algorithm)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($algorithm)), E_USER_ERROR);
        }

        $this -> driverInstance = null;        
        $this -> algorithm = $algorithm;

        return $this;
    }


    /**
     * Set the amount of digits the OTP needs to contain
     * @param integer $digits
     * @return $this
     */
    public function setDigits($digits) {
        
        if(false === ('-' . intval($digits) == '-' . $digits)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($digits)), E_USER_ERROR);
        }
        
        $this -> driverInstance = null;
        $this -> digits = $digits;
        
        return $this;
    }


    /**
     * Set the interval for TOTP before the otp will expire
     * @param integer $interval
     * @return $this
     */
    public function setInterval($interval) {
        
        if(false === is_string($interval)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($interval)), E_USER_ERROR);
        }
        
        $this -> driverInstance = null;
        $this -> interval = $interval;
        
        return $this;
    }


    /**
     * Get the password for a specific timestamp value
     * @param integer $timestamp
     * @return integer
     */
    public function timestamp($timestamp) {

        if(false === ('-' . intval($timestamp) == '-' . $timestamp)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($timestamp)), E_USER_ERROR);
        }

        return $this -> call() -> timestamp($timestamp);
    }


    /**
     * Get the password for the current timestamp value
     * @return integer
     */
    public function now() {
        return $this -> call() -> now();
    }


    /**
     * Returns the uri for a specific secret for hotp method.
     * @param string $name
     * @param integer $data
     * @return string
     */
    public function getProvisioningUrl($name, $data = null) {

        if(false === is_string($name)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
        }

        return $this -> call() -> getProvisioningUrl($name, $data);
    }


    /**
     * Get the password for a specific counter value
     * @param integer $count 
     * @return integer
     */
    public function counter($count) {

        if(false === ('-' . intval($count) == '-' . $count)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($count)), E_USER_ERROR);
        }

        return $this -> call() -> counter($count);
    }


     /**
     * Verify if a password is valid for a specific counter value
     * @param integer $otp
     * @param integer $data 
     * @return boolean
     */
    public function verify($otp, $data = null) {

        if(false === ('-' . intval($otp) == '-' . $otp)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($otp)), E_USER_ERROR);
        }

        if(null !== $data && false === ('-' . intval($data) == '-' . $data)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
        }

        return $this -> call() -> verify($otp, $data);
    }


    /**
     * Check if OTP driver exists and returns the driver
     * @return mixed
     */
    private function call() {

        if(null === $this -> driver) {
            return trigger_error('Driver is not set. Set the driver with the setDriver() method', E_USER_ERROR);
        }

        if(null === $this -> secret) {
            return trigger_error('Secret key is not set. Set the key with the setSecret() method', E_USER_ERROR);
        }

        if(null === $this -> driverInstance) {

            $options = [
                
                'interval'  => $this -> interval,
                'algorithm' => $this -> algorithm,
                'digits'    => $this -> digits
            ];

        	$this -> driverInstance = new $this -> driver($this -> secret, $options);
        }

        return $this -> driverInstance;
    }
}