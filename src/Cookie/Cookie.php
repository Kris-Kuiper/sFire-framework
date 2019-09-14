<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Cookie;

use sFire\Container\Container;
use sFire\Hash\Hash;
use sFire\Application\Application;

final class Cookie extends Container {


	/**
	 * @var array $data
	 */
	protected static $data = [];


	/**
	 * Constructor
	 * @return sFire\Cookie\Cookie
	 */
	public function __construct() {
		static :: $data = &$_COOKIE;
	}


	/**
	 * Sets a new cookie
	 * @param string $key
	 * @param string $value
	 * @param integer $seconds
	 * @param boolean $encrypt
	 * @param string $path
	 * @param string $domain
	 * @param boolean $secure
	 * @param boolean $httponly
	 */
	public static function add($key, $value = null, $seconds = 0, $encrypt = false, $path = null, $domain = null, $secure = null, $httponly = null) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(null !== $value && false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		if(null !== $seconds && false === ('-' . intval($seconds) == '-' . $seconds)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($seconds)), E_USER_ERROR);
		}

		if(null !== $encrypt && false === is_bool($encrypt)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($encrypt)), E_USER_ERROR);
		}

		if(null !== $path && false === is_string($path)) {
			return trigger_error(sprintf('Argument 5 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($path)), E_USER_ERROR);
		}

		if(null !== $domain && false === is_string($domain)) {
			return trigger_error(sprintf('Argument 6 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
		}

		if(null !== $secure && false === is_bool($secure)) {
			return trigger_error(sprintf('Argument 7 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($secure)), E_USER_ERROR);
		}

		if(null !== $httponly && false === is_bool($httponly)) {
			return trigger_error(sprintf('Argument 8 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($httponly)), E_USER_ERROR);
		}

		$cookie = session_get_cookie_params(); 

		if(true === $encrypt) {

			if(null === Application :: get('salt')) {
				return trigger_error('You should specify a salt (string) in the Application config before encrypting Cookies', E_USER_ERROR);
			}

			$key 	= rtrim(base64_encode($key), '=');
			$value 	= base64_encode(Hash :: encrypt($value, Application :: get('salt')));
		}

		$path 		= $path ? $path : $cookie['path'];		
		$domain 	= $domain ? $domain : $cookie['domain'];		
		$secure 	= $secure ? $secure : $cookie['secure'];	
		$httponly 	= $httponly ? $httponly : $cookie['httponly'];	

		setcookie($key, $value, time() + $seconds, $path, $domain, $secure, $httponly);
	}


	/**
	 * Retrieve and delete an item 
	 * @param string $key
	 * @param default $mixed
	 * @return mixed
	 */
	public static function pull($key, $default = NULL) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(true === isset(static :: $data[$key])) {
			
			$default = static :: get($key);
			static :: add($key, null, -1);
		}

		return $default;
	}


	/**
	 * Remove data based on key 
	 * @param string $key
	 */
	public static function remove($key = null) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if($key && isset(static :: $data[$key])) {
			static :: add($key, null, -1);
		}
	}


	/**
	 * Deletes all data
	 */
	public static function flush() {
		
		foreach(static :: $data as $key => $value) {
			static :: add($key, null, -1);
		}
	}


	/**
	 * Retrieve data based on key
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($key, $default = null) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(true === isset(static :: $data[$key])) {
			return static :: $data[$key];
		}

		$key = rtrim(base64_encode($key), '=');

		if(true === isset(static :: $data[$key])) {
			
			$cookie = Hash :: decrypt(base64_decode(static :: $data[$key]), Application :: get('salt'));
			return false !== $cookie ? $cookie : $default;
		}

		return $default;
	}


	/**
	 * Get all data from current session Cookie
	 * @return array
	 */
	public static function all() {

		$cookies = [];

		foreach(static :: $data as $key => $value) {

			if(rtrim(base64_encode(base64_decode($key, true)), '=') === $key) {
				$cookies[base64_decode($key)] = Hash :: decrypt(static :: $data[$key], Application :: get('salt'));
			}
			else {
				$cookies[$key] = $value;
			}
		}

		return $cookies;
	}
}
?>