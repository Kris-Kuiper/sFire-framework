<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Config;

use sFire\Container\Container;

final class Config extends Container {

	/**
	 * @var array $data 
	 */
	protected static $data = [];


	/**
	 * Flush method is not allowed
	 * @return \BadMethodCallException
	 */
	public static function flush() {
		return trigger_error(sprintf('Unsupported method %s called', __METHOD__), E_USER_ERROR);
	}


	/**
	 * Forget method is not allowed
	 * @return \BadMethodCallException
	 */
	public static function forget() {
		return trigger_error(sprintf('Unsupported method %s called', __METHOD__), E_USER_ERROR);
	}
}