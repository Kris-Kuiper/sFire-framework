<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Cache\Driver;

use sFire\Cache\CacheInterface;
use sFire\System\File;

final class APCu implements CacheInterface {
	

	/**
	 * Constructor
	 */
	public function __construct() {

		if(false === extension_loaded('apcu')) {
			return trigger_error('Can not use APCu. APCu is not installed.', E_USER_ERROR);
		}
	}


	/**
	 * Set new cache by key name
	 * @param mixed $key
	 * @param mixed $value
	 * @param int $expiration
	 * @return sFire\Cache\APCu
	 */
	public function set($key, $value, $expiration = 300) {

		if(false === is_string($key) && false === is_array($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
   		}

   		if(0 === strlen($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() may not be an empty string', __METHOD__), E_USER_ERROR);
   		}
   		
		$this -> expire($key);
		apcu_add($key, $value, $expiration);

		return $this;
	}


	/**
	 * Returns the cache if available, otherwise returns the default parameter
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = null) {

		if(false === is_string($key) && false === is_array($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
   		}

   		if(0 === strlen($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() may not be an empty string', __METHOD__), E_USER_ERROR);
   		}

		$result = apcu_fetch($key, $success);

		if(true === $success) {
			return $result;
		}

		return $default;
	}


	/**
	 * Expire cache based on key
	 * @param mixed $key
	 * @return sFire\Cache\APCu
	 */
	public function expire($key) {

		if(false === is_string($key) && false === is_array($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
   		}

   		if(0 === strlen($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() may not be an empty string', __METHOD__), E_USER_ERROR);
   		}

		apcu_delete($key);
		return $this;
	}


	/**
	 * Clear all cache files
	 * @return sFire\Cache\APCu
	 */
	public function clear() {

		apcu_clear_cache();
		return $this;
	}


	/**
	 * Reset lifetime of a cached file
	 * @param mixed $key
	 * @param int $expiration
	 * @return sFire\Cache\APCu
	 */
	public function touch($key, $expiration = null) {

		if(false === is_string($key) && false === is_array($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
   		}

   		if(0 === strlen($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() may not be an empty string', __METHOD__), E_USER_ERROR);
   		}

		if(true === $this -> exists($key)) {

			$info = apcu_cache_info();

			if(true === is_array($info) && true === isset($info['cache_list'])) {

				foreach($info['cache_list'] as $index) {

					if(true === isset($index['info'], $index['ttl']) && $index['info'] === $key) {

						$data 		= $this -> get($key);
						$expiration = $index['ttl'];

						$this -> expire($key);
						$this -> set($key, $data, $expiration);
						break;
					}
				}
			}
		}

		return $this;
	}


	/**
	 * Returns if a cache file exists based on key
	 * @param mixed $key
	 * @return boolean
	 */
	public function exists($key) {

		if(false === is_string($key) && false === is_array($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
   		}

   		if(0 === strlen($key)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() may not be an empty string', __METHOD__), E_USER_ERROR);
   		}

		return apcu_exists($key);
	}
}