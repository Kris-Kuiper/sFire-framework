<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Cache;

use sFire\Cache\CacheInterface;

class Cache implements CacheInterface {


	/**
     * @var string $driver
     */
    private $driver;


    /**
     * @var mixed $driverInstance
     */
    private $driverInstance;


	/**
	 * Sets the driver
	 * @param string $driver
	 * @return sFire\Cache\Cache
	 */
    public function setDriver($driver) {

    	if(false === is_string($driver)) {
    		return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($driver)), E_USER_ERROR);
    	}

        if(false === class_exists($driver)) {

            $driver = __NAMESPACE__ . '\\Driver\\' . $driver;
        
            if(false === class_exists($driver)) {
        		return trigger_error(sprintf('Driver "%s" does not exists', $driver), E_USER_ERROR);
        	}
        }

        $this -> driver = $driver;

        return $this;
    }


    /**
     * Set new cache by key name
     * @param mixed $key
     * @param mixed $value
     * @param int $expiration
     * @return sFire\Cache\Cache
     */
    public function set($key, $value, $expiration = 300) {

    	if(false === ('-' . intval($expiration) == '-' . $expiration)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($expiration)), E_USER_ERROR);
		}
    	
    	$this -> call() -> set($key, $value, $expiration);
    	return $this;
    }


    /**
     * Returns the cache if available, otherwise returns the default parameter
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
    	return $this -> call() -> get($key, $default);
    }


    /**
     * Expire cache based on key
     * @param mixed $key
     * @return sFire\Cache\Cache
     */
    public function expire($key) {
		
		$this -> call() -> expire($key);
		return $this;
    }


    /**
     * Clear all cache files
     * @return sFire\Cache\Cache
     */
    public function clear() {
		
		$this -> call() -> clear();
		return $this;
    }


    /**
     * Reset lifetime of a cached file
     * @param mixed $key
     * @param int $expiration
     * @return sFire\Cache\Cache
     */
    public function touch($key, $expiration = null) {

    	if(null !== $expiration && false === ('-' . intval($expiration) == '-' . $expiration)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($expiration)), E_USER_ERROR);
		}

		$this -> call() -> touch($key, $expiration);
		return $this;
    }


    /**
     * Returns if a cache exists based on key
     * @return boolean
     */
    public function exists($key) {
    	return $this -> call() -> exists($key);
    }


    /**
     * Check if driver is set and returns it
     * @return mixed
     */
    private function call() {

        if(null === $this -> driver) {
            return trigger_error('Driver is not set. Set the driver with the setDriver() method', E_USER_ERROR);
        }

        if(null === $this -> driverInstance) {
        	$this -> driverInstance = new $this -> driver();
        }

        return $this -> driverInstance;
    }
}