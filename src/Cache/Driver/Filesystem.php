<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Cache\Driver;

use sFire\Cache\CacheInterface;
use sFire\Config\Path;
use sFire\System\File;
use sFire\Application\Application;

final class Filesystem implements CacheInterface {
	

	/**
	 * @var int $probability
	 */
	private $probability;


	/**
	 * Construtor
	 * @return sFire\Cache\Filesystem
	 */
	public function __construct() {

		if(false === is_writable(Path :: get('cache-shared'))) {
			trigger_error(sprintf('Cache folder "%s" is not writable', Path :: get('cache-shared')), E_USER_ERROR);
		}

		if(false === is_readable(Path :: get('cache-shared'))) {
			trigger_error(sprintf('Cache folder "%s" is not readable', Path :: get('cache-shared')), E_USER_ERROR);
		}

		$this -> probability = Application :: get(['cache', 'probability'], 5);

		$this -> garbage();
	}


	/**
	 * Set new cache by key name
	 * @param mixed $key
	 * @param mixed $value
	 * @param int $expiration
	 * @return sFire\Cache\Filesystem
	 */
	public function set($key, $value, $expiration = 300) {

		if(false === ('-' . intval($expiration) == '-' . $expiration)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($expiration)), E_USER_ERROR);
		}

		$files = glob(Path :: get('cache-shared') . $this -> generateName($key));

		if(count($files) > 0 && true === is_array($files)) {
			
			foreach($files as $file) {
				
				$file = new File($file);
				$file -> delete();
			}
		}

		$cache = new File(Path :: get('cache-shared') . $this -> generateName($key, intval($expiration)));
		$cache -> create() -> append(serialize($value));

		return $this;
	}


	/**
	 * Returns the cache if available, otherwise returns the default parameter
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = null) {

		$files = glob(Path :: get('cache-shared') . $this -> generateName($key));

		if(true === is_array($files) && count($files) > 0) {

			$file 		= new File($files[0]);
			$expiration = $this -> extractExpiration($file);

			if($expiration -> time >= time()) {
				return unserialize($file -> getContent());
			}

			$file -> delete();
		}

		return $default;
	}


	/**
	 * Expire cache based on key
	 * @param mixed $key
	 * @return sFire\Cache\Files
	 */
	public function expire($key) {

		$files = glob(Path :: get('cache-shared') . $this -> generateName($key));

		if(true === is_array($files) && count($files) > 0) {

			$file = new File($files[0]);
			$file -> delete();
		}

		return $this;
	}


	/**
	 * Clear all cache files
	 * @return sFire\Cache\Files
	 */
	public function clear() {

		$files = glob(Path :: get('cache-shared') . '*');

		if(true === is_array($files)) {

			foreach($files as $file) {

				$file = new File($file);
				$file -> delete();
			}
		}

		return $this;
	}


	/**
	 * Clear all expired cache
	 * @return sFire\Cache\Files
	 */
	public function clearExpired() {

		$files = glob(Path :: get('cache-shared') . '*');
		
		if(true === is_array($files) && count($files) > 0) {

			foreach($files as $file) {
			  	
			  	$file 		= new File($file);
				$expiration = $this -> extractExpiration($file);

				if($expiration -> time <= time()) {
					$file -> delete();
				}
			}
		}
		
		return $this;
	}


	/**
	 * Reset lifetime of a cached file
	 * @param mixed $key
	 * @param int $expiration
	 * @return sFire\Cache\Files
	 */
	public function touch($key, $expiration = null) {

		if(null !== $expiration && false === ('-' . intval($expiration) == '-' . $expiration)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($expiration)), E_USER_ERROR);
		}

		$files = glob(Path :: get('cache-shared') . $this -> generateName($key));

		if(true === is_array($files) && count($files) > 0) {

			$file = new File($files[0]);
			
			if(null === $expiration) {
				
				$expiration = $this -> extractExpiration($file);
				$expiration = $expiration -> expiration;
			}

			$file -> rename($this -> generateName($key, $expiration));
		}

		return $this;
	}


	/**
	 * Returns if a cache file exists based on key
	 * @param mixed $key
	 * @return boolean
	 */
	public function exists($key) {

		$files = glob(Path :: get('cache-shared') . $this -> generateName($key));

		if(true === is_array($files) && count($files) > 0) {

			$file 		= new File($files[0]);
			$expiration = $this -> extractExpiration($file);

			if($expiration -> time >= time()) {
				return true;
			}

			$file -> delete();
		}

		return false;
	}


	/**
	 * Generates the cache file name
	 * @param mixed $key
	 * @param int $expiration
	 * @return string
	 */
	private function generateName($key, $expiration = null) {

		if(null !== $expiration && false === ('-' . intval($expiration) == '-' . $expiration)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($expiration)), E_USER_ERROR);
		}
		
		return md5(serialize($key)) . '-' . ($expiration ? $expiration : '*') . '-' . ($expiration ? (time() + $expiration) : '*') . Application :: get(['extensions', 'cache']);
	}


	/**
	 * Returns the expiration time of cache file
	 * @param sFire\System\File $file
	 * @return object
	 */
	private function extractExpiration(File $file) {

		$expiration = explode('-', $file -> entity() -> getName());

		if(count($expiration) > 2) {
			return (object) ['time' => $expiration[2], 'expiration' => $expiration[1]];
		}

		return (object) ['time' => 0, 'expiration' => 0];
	}


	/**
	 * Clears all expired cache files based on a probability
	 */
	private function garbage() {

		if(1 === mt_rand(1, $this -> probability)) {
			$this -> clearExpired();
		}
	}
}
?>