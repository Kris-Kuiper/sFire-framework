<?php 
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\MVC;

final class MiddlewareContainer {


	/**
	 * @var array $before
	 */
	private static $before = [];


	/**
	 * @var array $after
	 */
	private static $after = [];


	/**
	 * @var string $modus
	 */
	private static $modus = 'before';


	/**
	 * @var array $namespaces
	 */
	private static $namespaces = [];


	/**
	 * @var array $matches
	 */
	private static $matches = [];
	

	/**
	 * @var array $counter
	 */
	private static $counter = [
		
		'before' => -1,
		'after' => -1
	];


	/**
	 * Sets the route variables to inject into the next middleware that needs to be executed
	 * @param array $matches
	 */
	public static function matches($matches) {

		if(false === is_array($matches)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($matches)), E_USER_ERROR);
		}

		static :: $matches = $matches;
	}


	/**
	 * Add a new middleware class with a type (before or after), so this class with an before or after method will execute before of after the controller
	 * @param string $namespace
	 * @param string $type
	 */
	public static function add($namespace, $type) {

		if(false === is_string($namespace)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($namespace)), E_USER_ERROR);
		}

		if(false === is_string($type)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(false === in_array($type, ['before', 'after'])) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be either "before" or "after", "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(true === is_callable([$namespace, $type])) {
				
			if(false === isset(static :: $namespaces[$namespace])) {
				static :: $namespaces[$namespace] = new $namespace;
			}
			
			switch($type) {

				case 'before': static :: $before[] = static :: $namespaces[$namespace]; break;
				case 'after': static :: $after[] = static :: $namespaces[$namespace]; break;
			}
		}
	}


	/**
	 * Check if there is more middleware to execute
	 * @return boolean
	 */
	public static function isEmpty() {

		switch(static :: $modus) {

			case 'before': return static :: $counter[static :: $modus] === count(static :: $before); break;
			case 'after': return static :: $counter[static :: $modus] === count(static :: $after); break;
		}
	}


	/**
	 * Execute the next middleware if available
	 */
	public static function next() {
		
		switch(static :: $modus) {

			case 'before': 
				
				static :: $counter['before']++;

				if(true === isset(static :: $before[static :: $counter['before']])) {

					$middleware = static :: $before[static :: $counter['before']];
					
					//Execute method if middleware supports it
					call_user_func_array([$middleware, 'before'], static :: $matches);
				}

			break;

			case 'after': 
				
				static :: $counter['after']++;

				if(true === isset(static :: $after[static :: $counter['after']])) {

					$middleware = static :: $after[static :: $counter['after']];
					
					//Execute method if middleware supports it
					call_user_func_array([$middleware, 'after'], static :: $matches);
				}

			break;
		}
	}


	/**
	 * Set the current modus
	 * @param string $modus
	 */
	public static function modus($modus) {

		if(false === is_string($modus)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($modus)), E_USER_ERROR);
		}

		if(false === in_array($modus, ['before', 'after'])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be either "before" or "after", "%s" given', __METHOD__, gettype($modus)), E_USER_ERROR);
		}

		static :: $modus = $modus;
	}
}