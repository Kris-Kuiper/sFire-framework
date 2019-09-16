<?php 
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\MVC;

use sFire\Routing\Router;
use sFire\Utils\StringToArray;
use sFire\Validator\Form\Message;
use sFire\HTTP\Request;
use sFire\Application\Application;

trait MVCTrait {
	

	/**
	 * Loads a helper class in current module directory
	 * @param string $classname
	 * @return Object
	 */
	protected function helper($classname) {

		$directories 	= explode('.', $classname); //Convert dots to directory seperators
		$amount 		= count($directories) - 1;
		$namespace 		= Router :: getRoute() -> getModule() . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', Application :: get(['directory', 'helper']));

		foreach($directories as $index => $directory) {

			if($amount === $index) {
				
				$namespace .= Application :: get(['prefix', 'helper']) . $directory;
				break;
			}

			$namespace 	.= $directory . '\\';
		}

		return new $namespace;
	}


	/**
	 * Get the error message from the validator by fieldname
	 * @param string $fieldname
	 * @return string
	 */
	protected function fails($fieldname = null) {
		
		$helper = new StringToArray();
		return $helper -> execute($fieldname, null, Message :: getErrors(true));
	}


	/**
	 * Returns if there is no validation error for the given fieldname when request method is equal to POST
	 * @param string $fieldname
	 * @return boolean
	 */
	protected function passes($fieldname = null) {
		
		$helper = new StringToArray();
		return true === Request :: isPost() && null === $helper -> execute($fieldname, null, Message :: getErrors(true));
	}
}