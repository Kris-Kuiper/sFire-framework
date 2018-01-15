<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Container;

interface ContainerInterface {


	/**
	 * Stores a new piece of data and tries to merge the data if already exists
	 * @param string $key
	 * @param mixed $value
	 */
	public static function add($key, $value);

	
	/**
	 * Stores a new piece of data and overwrites the data if already exists
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set($key, $value);


	/**
	 * Check if an item exists
	 * @param string $key
	 * @return boolean
	 */
	public static function has($key);


	/**
	 * Deletes all data
	 */
	public static function flush();


	/**
	 * Remove data based on key
	 * @param string $key
	 */
	public static function remove($key);


	/**
	 * Retrieve data based on key. Returns $default if not exists
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($key, $default = null);


	/**
	 * Retrieve and delete an item. Returns $default if not exists
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function pull($key, $default = null);


	/**
	 * Retrieve all data
	 * @return mixed
	 */
	public static function all();
}
?>