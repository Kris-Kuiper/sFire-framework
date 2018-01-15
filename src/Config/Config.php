<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
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
?>