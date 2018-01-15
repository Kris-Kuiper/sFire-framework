<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Cache;

class Cache {


	/**
	 * @var mixed $driver
	 */
	private $driver;


	/**
	 * Constructor
	 * @param mixed $driver
	 */
	public function __construct($driver = null) {
		
		if(null !== $driver) {
			$this -> driver = $driver;
		}
	}


	/**
	 * Sets the driver
	 * @param mixed $driver
	 * @return sFire\Cache\Cache
	 */
	public function setDriver($driver) {
		
		$this -> driver = $driver;
		return $this;
	}


	/**
	 * Returns the driver
	 * @return mixed
	 */
	public function getDriver() {
		return $this -> driver;
	}


	/**
	 * Universal method for calling methods of the driver
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public function __call($method, $params) {

		if(null === $this -> getDriver()) {
			return trigger_error('Driver is not set. Set the driver with the setDriver() method', E_USER_ERROR);
		}

		$driver = $this -> getDriver();

		if(false === is_callable([$driver, $method])) {

			$class = new \ReflectionClass($driver);
			$type = $class->getName();

			trigger_error(sprintf('Driver "%s" does not have the "%s" method ', $type, $method), E_USER_ERROR);
		}

		return call_user_func_array([$driver, $method], $params);
	}
}
?>