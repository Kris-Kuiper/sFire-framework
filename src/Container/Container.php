<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Container;

use sFire\Container\AbstractContainer;
use sFire\Container\ContainerInterface;

class Container extends AbstractContainer implements ContainerInterface {
	
	
	/**
	 * Stores a new piece of data and tries to merge the data if already exists
	 * @param string|array $key
	 * @param mixed $value
	 */
	public static function add($key, $value) {

		if(null === $value && is_array($key)) {

			foreach($key as $index => $value) {
				static :: $data[$index] = $value;
			}

			return;
		}

		if(true === isset(static :: $data[$key]) && true === is_array(static :: $data[$key])) {

			if(true === is_array($value)) {
				$value = array_merge(static :: $data[$key], $value);	
			}
			else {
				static :: $data[$key][] = $value;
			}
		}

		static :: $data[$key] = $value;
	}


	/**
	 * Stores a new piece of data and overwrites the data if already exists
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set($key, $value) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		static :: $data[$key] = $value;
	}


	/**
	 * Check if an item exists
	 * @param string $key
	 * @return boolean
	 */
	public static function has($key) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		return isset(static :: $data[$key]);
	}


	/**
	 * Deletes all data
	 */
	public static function flush() {
		static :: $data = [];
	}


	/**
	 * Remove data based on key
	 * @param string $key
	 */
	public static function remove($key) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if($key && isset(static :: $data[$key])) {
			unset(static :: $data[$key]);
		}
	}


	/**
	 * Retrieve data based on key. Returns $default if not exists
	 * @param string|array $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($key, $default = null) {

		if(true === is_array($key)) {

			$tmp = static :: $data;

			foreach($key as $index) {

				if(false === isset($tmp[$index])) {
					return $default;
				}

				$tmp = $tmp[$index];
			}

			return $tmp;
		}

		if(true === isset(static :: $data[$key])) {
			return static :: $data[$key];
		}

		return $default;
	}


	/**
	 * Retrieve and delete an item. Returns $default if not exists
	 * @param string|array $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function pull($key, $default = null) {

		if(true === is_string($key)) {
			$key = [$key];
		}

		$tmp 	=& static :: $data;
		$amount = count($key);
		$value 	= $default;

		foreach($key as $i => $index) {

			if(true === is_string($index) || true === is_int($index)) {
				
				if(true === isset($tmp[$index])) {

					if($i === $amount - 1) {

						$value = $tmp[$index];
						unset($tmp[$index]);
					}
					else {
						$tmp =& $tmp[$index];
					}
				}
			}
		}

		return $value;
	}


	/**
	 * Retrieve all data
	 * @return mixed
	 */
	public static function all() {
		return static :: $data;
	}
}