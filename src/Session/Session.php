<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Session;

use sFire\Session\AbstractSession;

class Session {


	/**
     * @param string $driver
     */
    private $driver;


    /**
     * @param mixed $driverInstance
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
	 * Stores a new piece of data and tries to merge the data if already exists
	 * @param string|array $key
	 * @param mixed $value
	 */
	public function add($key, $value) {

		$this -> call() -> add($key, $value);
		return $this;
	}


	/**
	 * Stores a new piece of data and overwrites the data if already exists
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {

		$this -> call() -> set($key, $value);
		return $this;
	}


	/**
	 * Check if an item exists
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return $this -> call() -> has($key);
	}


	/**
	 * Deletes all data
	 */
	public function flush() {

		$this -> call() -> flush();
		return $this;
	}


	/**
	 * Remove data based on key
	 * @param string $key
	 */
	public function remove($key) {

		$this -> call() -> remove($key);
		return $this;
	}


	/**
	 * Retrieve data based on key. Returns $default if not exists
	 * @param string|array $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = null) {
		return $this -> call() -> get($key, $default);
	}


	/**
	 * Retrieve and delete an item. Returns $default if not exists
	 * @param string|array $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function pull($key, $default = null) {
		return $this -> call() -> pull($key, $default);
	}


	/**
	 * Retrieve all data
	 * @return mixed
	 */
	public function all() {
		return $this -> call() -> all();
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
?>