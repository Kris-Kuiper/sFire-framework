<?php 
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\MVC;

use sFire\Application\Application;

trait NamespaceTrait {


	/**
	 * Returns the path of a giving namespace
	 * @param object $class
	 * @param array $source
	 * @param array $destination
	 * @return string
	 */
	private function getNamespace($class, $source, $destination) {

		if(false === is_object($class)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type object, "%s" given', __METHOD__, gettype($class)), E_USER_ERROR);
		}

		if(false === is_array($source)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($source)), E_USER_ERROR);
		}

		if(false === is_array($destination)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($destination)), E_USER_ERROR);
		}

		$class 		= new \ReflectionClass($class);
		$current 	= str_replace('\\', DIRECTORY_SEPARATOR, $class -> getNamespaceName()) . DIRECTORY_SEPARATOR;
		$folder 	= Application :: get($source);
		$module 	= rtrim(str_replace($folder, '', $current), DIRECTORY_SEPARATOR);
		$path 		= $module . DIRECTORY_SEPARATOR . Application :: get($destination);

		return $path;
	}
}
?>