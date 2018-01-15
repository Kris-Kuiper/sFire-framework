<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Cache;

interface CacheInterface {

	/**
	 * Constructor
	 */
	public function __construct();
	

	/**
	 * Set new cache by key name
	 * @param mixed $key
	 * @param mixed $value
	 * @param int $expiration
	 * @return $this
	 */
	public function set($key, $value, $expiration = null);


	/**
	 * Returns the cache if available, otherwise returns the default parameter
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = null);


	/**
	 * Expire cache based on key
	 * @param mixed $key
	 * @return $this
	 */
	public function expire($key);


	/**
	 * Clear all cache files
	 * @return $this
	 */
	public function clear();


	/**
	 * Reset lifetime of a cached file
	 * @param mixed $key
	 * @param int $expiration
	 * @return $this
	 */
	public function touch($key, $expiration = null);


	/**
	 * Returns if a cache exists based on key
	 * @return boolean
	 */
	public function exists($key);
}
?>